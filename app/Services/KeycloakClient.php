<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use RuntimeException;

class KeycloakClient
{
    public function authorizationUrl(Request $request): string
    {
        abort_unless(config('sso.enabled'), 503, 'SSO belum diaktifkan.');

        $state = bin2hex(random_bytes(32));
        $verifier = bin2hex(random_bytes(32));
        $nonce = bin2hex(random_bytes(32));
        $request->session()->put('oidc.pending', compact('state', 'verifier', 'nonce') + ['created' => time()]);

        return $this->endpoint('auth') . '?' . http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge_method' => 'S256',
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
        ]);
    }

    public function callback(Request $request): array
    {
        $pending = $request->session()->pull('oidc.pending');
        if (! is_array($pending) || time() - ($pending['created'] ?? 0) > 300
            || ! is_string($request->query('state'))
            || ! hash_equals($pending['state'] ?? '', $request->query('state'))
            || ! is_string($request->query('code'))
            || $request->query('error')) {
            throw new RuntimeException('Callback SSO tidak valid atau kedaluwarsa.');
        }

        $response = Http::asForm()->timeout(10)->post($this->endpoint('token'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('sso.client_id'),
            'client_secret' => config('sso.client_secret'),
            'redirect_uri' => config('sso.redirect_uri'),
            'code' => $request->query('code'),
            'code_verifier' => $pending['verifier'],
        ]);
        if (! $response->successful()) {
            throw new RuntimeException('Pertukaran kode SSO gagal.');
        }

        $tokens = $response->json();
        $claims = $this->validateIdToken((string) ($tokens['id_token'] ?? ''), $pending['nonce']);
        if (empty($tokens['access_token'])) {
            throw new RuntimeException('Access token SSO tidak tersedia.');
        }

        return ['claims' => $claims, 'id_token' => $tokens['id_token'], 'access_token' => $tokens['access_token']];
    }

    public function validateIdToken(string $token, string $nonce): array
    {
        $response = Http::timeout(10)->get($this->endpoint('certs'));
        if (! $response->successful()) {
            throw new RuntimeException('Kunci Keycloak tidak tersedia.');
        }

        $keys = array_values(array_filter(
            $response->json('keys') ?? [],
            fn ($key) => ($key['kty'] ?? '') === 'RSA'
                && ($key['alg'] ?? '') === 'RS256'
                && ($key['use'] ?? 'sig') === 'sig'
        ));
        $parsedToken = (new Parser(new JoseEncoder()))->parse($token);
        if (! $parsedToken instanceof Plain) {
            throw new RuntimeException('Format token SSO tidak valid.');
        }
        $key = collect($keys)->firstWhere('kid', $parsedToken->headers()->get('kid'));
        if (! $key || ! (new Sha256())->verify(
            // Verifikasi RSA memakai byte tanda tangan asli; toString() masih berupa Base64URL.
            $parsedToken->signature()->hash(),
            $parsedToken->payload(),
            InMemory::plainText($this->jwkToPem($key))
        )) {
            throw new RuntimeException('Tanda tangan token SSO tidak valid.');
        }

        $claims = $parsedToken->claims()->all();
        $audiences = (array) ($claims['aud'] ?? []);
        $expiresAt = $claims['exp'] instanceof \DateTimeInterface ? $claims['exp']->getTimestamp() : ($claims['exp'] ?? 0);
        $issuedAt = $claims['iat'] instanceof \DateTimeInterface ? $claims['iat']->getTimestamp() : ($claims['iat'] ?? 0);
        if (($claims['iss'] ?? '') !== rtrim(config('sso.issuer'), '/')
            || ! in_array(config('sso.client_id'), $audiences, true)
            || (isset($claims['azp']) && $claims['azp'] !== config('sso.client_id'))
            || (count($audiences) > 1 && ! isset($claims['azp']))
            || ! is_string($claims['sub'] ?? null) || $claims['sub'] === ''
            || ! is_numeric($expiresAt) || $expiresAt <= time()
            || ! is_numeric($issuedAt) || $issuedAt > time() + 30
            || ! is_string($claims['nonce'] ?? null) || ! hash_equals($nonce, $claims['nonce'])) {
            throw new RuntimeException('Identitas SSO tidak valid.');
        }

        return $claims;
    }

    private function jwkToPem(array $key): string
    {
        $modulus = $this->base64UrlDecode((string) ($key['n'] ?? ''));
        $exponent = $this->base64UrlDecode((string) ($key['e'] ?? ''));
        if ($modulus === '' || $exponent === '') {
            throw new RuntimeException('Kunci publik Keycloak tidak valid.');
        }

        $rsaKey = $this->asn1Sequence($this->asn1Integer($modulus) . $this->asn1Integer($exponent));

        return "-----BEGIN RSA PUBLIC KEY-----\n"
            . chunk_split(base64_encode($rsaKey), 64, "\n")
            . "-----END RSA PUBLIC KEY-----\n";
    }

    private function base64UrlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);
        if ($decoded === false) {
            throw new RuntimeException('Format kunci publik Keycloak tidak valid.');
        }

        return $decoded;
    }

    private function asn1Integer(string $value): string
    {
        if (ord($value[0]) > 0x7f) {
            $value = "\x00" . $value;
        }

        return "\x02" . $this->asn1Length(strlen($value)) . $value;
    }

    private function asn1Sequence(string $value): string
    {
        return "\x30" . $this->asn1Length(strlen($value)) . $value;
    }

    private function asn1Length(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        $encoded = ltrim(pack('N', $length), "\x00");

        return chr(0x80 | strlen($encoded)) . $encoded;
    }

    public function isActive(string $token): bool
    {
        $response = Http::asForm()->timeout(5)->post($this->endpoint('token/introspect'), [
            'token' => $token,
            'client_id' => config('sso.client_id'),
            'client_secret' => config('sso.client_secret'),
        ]);
        if (! $response->successful()) {
            throw new RuntimeException('Layanan SSO tidak dapat diperiksa.');
        }

        return $response->json('active') === true;
    }

    public function logoutUrl(?string $idToken): string
    {
        return $this->endpoint('logout') . '?' . http_build_query([
            'client_id' => config('sso.client_id'),
            'id_token_hint' => $idToken,
            'post_logout_redirect_uri' => config('sso.logout_uri'),
        ]);
    }

    private function endpoint(string $path): string
    {
        $issuer = rtrim((string) config('sso.issuer'), '/');
        $parts = parse_url($issuer);
        if (! $issuer || empty($parts['host'])
            || (($parts['scheme'] ?? '') !== 'https'
                && ! (app()->environment('local', 'testing')
                    && in_array($parts['host'], config('sso.http_allowed_hosts', ['localhost', '127.0.0.1']), true)))) {
            throw new RuntimeException('Konfigurasi issuer SSO tidak aman.');
        }

        return $issuer . '/protocol/openid-connect/' . $path;
    }
}