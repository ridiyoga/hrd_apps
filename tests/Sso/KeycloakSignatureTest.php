<?php
namespace Tests\Sso;

use App\Services\KeycloakClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KeycloakSignatureTest extends TestCase
{
    private mixed $signingKey = null;
    private function token(array $override = [], bool $tampered = false): string
    {
        $key = $this->signingKey ??= openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA, 'config' => __DIR__.'/openssl.cnf']);
        $details = openssl_pkey_get_details($key);
        $b64 = fn ($v) => rtrim(strtr(base64_encode($v), '+/', '-_'), '=');
        Http::fake(['*/certs' => Http::response(['keys' => [[
            'kty'=>'RSA', 'alg'=>'RS256', 'use'=>'sig', 'kid'=>'test-key',
            'n'=>$b64($details['rsa']['n']), 'e'=>$b64($details['rsa']['e']),
        ]]])]);
        $payload = $b64(json_encode(['alg'=>'RS256','kid'=>'test-key'])).'.'.$b64(json_encode(array_replace([
            'iss'=>config('sso.issuer'), 'sub'=>'subject-1', 'aud'=>'hrd',
            'iat'=>time(), 'exp'=>time()+300, 'nonce'=>'expected-nonce',
        ], $override)));
        openssl_sign($payload, $signature, $key, OPENSSL_ALGO_SHA256);
        if ($tampered) { $signature[0] = chr(ord($signature[0]) ^ 1); }
        return $payload.'.'.$b64($signature);
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['sso.issuer'=>'http://127.0.0.1:8080/realms/internal', 'sso.client_id'=>'hrd']);
        Http::preventStrayRequests();
    }

    public function test_valid_rsa_signature_is_accepted(): void
    {
        $this->assertSame('subject-1', app(KeycloakClient::class)->validateIdToken($this->token(), 'expected-nonce')['sub']);
    }

    public function test_tampered_signature_is_rejected(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Tanda tangan token SSO tidak valid.');
        app(KeycloakClient::class)->validateIdToken($this->token([], true), 'expected-nonce');
    }

    public function test_wrong_claims_are_rejected(): void
    {
        foreach ([['iss'=>'https://other.example'], ['aud'=>'agile'], ['nonce'=>'wrong'], ['exp'=>time()-60]] as $override) {
            try {
                app(KeycloakClient::class)->validateIdToken($this->token($override), 'expected-nonce');
                $this->fail('Invalid identity accepted');
            } catch (\RuntimeException $e) {
                $this->assertSame('Identitas SSO tidak valid.', $e->getMessage());
            }
        }
    }
}
