<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Services\KeycloakClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SsoController extends Controller
{
    public function redirect(Request $request, KeycloakClient $oidc)
    {
        try {
            return redirect()->away($oidc->authorizationUrl($request));
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['username' => 'SSO belum tersedia. Hubungi administrator.']);
        }
    }

    public function callback(Request $request, KeycloakClient $oidc)
    {
        abort_unless(config('sso.enabled'), 404);

        try {
            $result = $oidc->callback($request);
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['username' => 'Login SSO gagal atau kedaluwarsa. Silakan ulangi.']);
        }

        $identity = DB::table('external_identities')
            ->where('issuer', rtrim(config('sso.issuer'), '/'))
            ->where('subject', $result['claims']['sub'])
            ->first();
        $user = $identity ? User::find($identity->user_id) : null;

        if (! $user) {
            abort(403, 'Identitas SSO belum dihubungkan ke akun HRD aktif.');
        }
        if ($user->user_aktif === 'NON AKTIF') {
            abort(403, 'Akun HRD tidak aktif.');
        }
        if ($user->is_admin === 'user') {
            $karyawan = Karyawan::where('nomor_identitas_karyawan', $user->karyawan_id)->first();
            if (! $karyawan || $karyawan->status_aktif === 'NON AKTIF') {
                abort(403, 'Data karyawan tidak ditemukan atau tidak aktif.');
            }
        }

        // Izin HRD harus ada di portal, selain pemeriksaan status pengguna/karyawan HRD di atas.
        try { $allowed = app(\App\Services\PortalAccess::class)->allows($result['claims']['sub']); }
        catch (\Throwable $e) { abort(503, 'Portal tidak dapat memeriksa akses HRD.'); }
        abort_unless($allowed, 403, 'Anda belum diberi akses HRD di portal.');
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('sso', [
            'subject' => $result['claims']['sub'],
            'access_token' => $result['access_token'],
            'id_token' => $result['id_token'],
        ]);

        return redirect()->intended('/');
    }

    public function logout(Request $request, KeycloakClient $oidc)
    {
        $idToken = $request->session()->get('sso.id_token');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away($idToken ? $oidc->logoutUrl($idToken) : route('login'));
    }
}