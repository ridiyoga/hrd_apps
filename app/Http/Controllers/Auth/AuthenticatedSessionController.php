<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // dd($request->authenticate());
        $request->authenticate();

        $request->session()->regenerate();
        $authUser = Auth::user();
        if ($authUser->user_aktif === 'NON AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Kita harus melemparkan ValidationException lagi
            // Tapi karena di Controller, kita bisa menggunakan return back() dengan error spesifik
            return back()->withErrors([
                'username' => 'Akun Anda non-aktif. Silakan hubungi administrator.',
            ]);
        }
        if ($authUser->is_admin === 'user') {
            $karyawan = Karyawan::find($authUser->karyawan_id);

            // Cek Karyawan Null atau Non Aktif
            if (!$karyawan || $karyawan->status_aktif === 'NON AKTIF') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = !$karyawan
                    ? 'Data karyawan tidak ditemukan.'
                    : 'Status karyawan Anda non-aktif.';

                return back()->withErrors(['username' => $errorMessage]);
            }

            // Alert::success('Berhasil', 'Selamat Datang');
            return redirect()->intended('/home'); // Redirect ke user home
        }
        return redirect()->intended(route('dashboard_holding', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
