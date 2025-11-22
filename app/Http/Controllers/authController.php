<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class authController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return Redirect::route('login');
        } else {
            if (Auth::user()->is_admin == 'admin') {
                return Redirect::route('dashboard_holding');
            } else if (Auth::user()->is_admin == 'hrd') {
                return Redirect::route('dashboard_holding');
            } else {
                return Redirect::route('home');
            }
        }
    }

    public function register()
    {
        return view('auth.register', [
            "title" => "Register Account"
        ]);
    }

    public function registerProses(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required|max:255",
            "email" => "required|email:dns|unique:users",
            "password" => "required|confirmed|min:6",
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);
        $request->session()->flash('success', 'Registrasi Berhasil! Silahkan Login.');
        return redirect('/');
    }


    public function loginProses(Request $request)
    {
        // 1. Definisi Pesan Kustom dan Validasi Input
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'email' => ':attribute format email salah',
        ];

        $credentials = $request->validate([
            'username' => 'required|string', // Menggunakan 'username' sebagai input umum
            'password' => 'required|string',
        ], $customMessages);

        $remember = $request->boolean('remember');

        // 2. Tentukan Field Otentikasi (Email atau Username)
        $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        // Buat array kredensial yang siap untuk Auth::attempt()
        // Kunci array harus sesuai dengan nama kolom di database.
        $attemptCredentials = [
            $field => $credentials['username'],
            'password' => $credentials['password'],
        ];

        // 3. Otentikasi Menggunakan Auth::attempt()
        // Laravel secara otomatis akan menghitung hash password dan membandingkannya
        if (! Auth::attempt($attemptCredentials, $remember)) {
            // Jika Auth::attempt() gagal, lempar ValidationException
            throw ValidationException::withMessages([
                'username' => __('Kredensial yang diberikan tidak cocok dengan catatan kami.'),
            ]);
        }

        // 4. Regenerate Session (Wajib setelah login sukses)
        $request->session()->regenerate();
        $authUser = Auth::user();

        // 5. Validasi Tambahan (User Aktif)
        if ($authUser->user_aktif === 'NON AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Lempar exception untuk pesan error yang lebih rapi
            throw ValidationException::withMessages([
                'username' => 'Akun Anda non-aktif. Silakan hubungi administrator.',
            ]);
            // return redirect('/')->with('user_nonaktif', true); // Alternatif redirect
        }

        // 6. Validasi dan Redireksi untuk Role 'user'
        if ($authUser->is_admin === 'user') {
            $karyawan = Karyawan::find($authUser->karyawan_id);

            if (!$karyawan || $karyawan->status_aktif === 'NON AKTIF') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = !$karyawan
                    ? 'Data karyawan tidak ditemukan.'
                    : 'Status karyawan Anda non-aktif. Silakan hubungi administrator.';

                throw ValidationException::withMessages([
                    'username' => $errorMessage,
                ]);
                // return redirect('/')->with('error_karyawan', true); // Alternatif redirect
            }

            // Alert::success('Berhasil', 'Selamat Datang');
            return redirect()->intended('/home'); // Menggunakan intended untuk mengarahkan ke URL yang terakhir ingin diakses
        }

        // 7. Redireksi untuk Role Selain 'user' (misalnya Admin)
        // Alert::success('Berhasil', 'Selamat Datang');
        return redirect()->intended('/dashboard/holding');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('logout_success');
        return redirect('/');
    }
}
