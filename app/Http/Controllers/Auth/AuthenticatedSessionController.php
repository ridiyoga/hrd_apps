<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // dd('ok');
        return view('auth.login');
    }
    public function create_user(): View
    {
        // dd('ok');
        return view('users.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // dd($request->all());
        $request->authenticate();

        $request->session()->regenerate();
        $authUser = Auth::user();
        // dd($authUser);
        if ($authUser->user_aktif === 'NON AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Kita harus melemparkan ValidationException lagi
            // Tapi karena di Controller, kita bisa menggunakan return back() dengan error spesifik
            $request->session()->flash('user_nonaktif', 'Akun Anda non-aktif. Silakan hubungi administrator.');
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda non-aktif. Silakan hubungi administrator.',
            ]);
        }
        if ($authUser->is_admin === 'user') {
            $karyawan = Karyawan::where('nomor_identitas_karyawan', $authUser->karyawan_id)->first();

            // Cek Karyawan Null atau Non Aktif
            if (!$karyawan || $karyawan->status_aktif === 'NON AKTIF') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = !$karyawan
                    ? 'Data karyawan tidak ditemukan.'
                    : 'Status karyawan Anda non-aktif.';
                ActivityLog::create([
                    'user_id' => $authUser->id,
                    'user_name' => $authUser->username,
                    'menu_name' => 'login',
                    'id_table' => $authUser->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'users',
                    'activity' => 'auth',
                    'karir' => '2',
                    'description' => 'gagal login karena karyawan non aktif (mobile)',
                    'keterangan' => 'Failed',

                ]);
                $request->session()->flash('user_nonaktif', 'Akun Anda non-aktif. Silakan hubungi administrator.');
                return redirect()->route('login')->withErrors(['username' => $errorMessage]);
            }

            // Alert::success('Berhasil', 'Selamat Datang');
            $home = Menu::where('url', 'home')->where('kategori', 'mobile')->where('main', 2)->where('is_active', 1)->first();
            $intended = $request->session()->get('url.intended');
            // dd($intended);
            if ($intended) {
                ActivityLog::create([
                    'user_id' => $authUser->id,
                    'user_name' => $authUser->username,
                    'menu_name' => 'login',
                    'id_table' => $authUser->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'users',
                    'activity' => 'auth',
                    'karir' => '2',
                    'description' => 'login menuju menu dari link yang diakses (mobile)',
                    'keterangan' => 'success',

                ]);
                User::where('id', $authUser->id)->update(['menu_active' => $home->id]);
                return redirect()->intended('/');
            } else {
                // dd($intended);
                if ($authUser->menu_active == null) {
                    User::where('id', $authUser->id)->update(['menu_active' => $home->id]);
                    ActivityLog::create([
                        'user_id' => $authUser->id,
                        'user_name' => $authUser->username,
                        'menu_name' => 'login',
                        'id_table' => $authUser->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'users',
                        'activity' => 'auth',
                        'karir' => '2',
                        'description' => 'login menuju menu terakhir yang diakses (mobile)',
                        'keterangan' => 'success',
                    ]);
                    return redirect('/home/' . $home->id); // Redirect ke user home
                } else {
                    $menu = Menu::where('id', $authUser->menu_active)->where('kategori', 'mobile')->where('main', 2)->where('is_active', 1)->first();
                    if ($menu == null) {
                        ActivityLog::create([
                            'user_id' => $authUser->id,
                            'user_name' => $authUser->username,
                            'menu_name' => 'login',
                            'id_table' => $authUser->id,
                            'holding_id' => '-',
                            'holding_category' => '-',
                            'table' => 'users',
                            'activity' => 'auth',
                            'karir' => '2',
                            'description' => 'login menuju menu terakhir yang diakses (mobile)',
                            'keterangan' => 'success',

                        ]);
                        User::where('id', $authUser->id)->update(['menu_active' => $home->id]);
                        return redirect('/home/' . $home->id);
                    } else {
                        ActivityLog::create([
                            'user_id' => $authUser->id,
                            'user_name' => $authUser->username,
                            'menu_name' => 'login',
                            'id_table' => $authUser->id,
                            'holding_id' => '-',
                            'holding_category' => '-',
                            'table' => 'users',
                            'activity' => 'auth',
                            'karir' => '2',
                            'description' => 'login berhasil (mobile)',
                            'keterangan' => 'success',

                        ]);
                        return redirect('/' . $menu->url . '/' . $menu->id); // Redirect ke user home
                    }
                }
            }
        }
        // dd($authUser->username);
        User::where('username', $request->username)->update(['menu_active' => NULL]);
        ActivityLog::create([
            'user_id' => $authUser->id,
            'user_name' => $authUser->username,
            'menu_name' => 'login',
            'id_table' => $authUser->id,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'users',
            'activity' => 'auth',
            'description' => 'berhasil login (PC/Komputer)',
            'keterangan' => 'Success',
        ]);
        return redirect()->intended(route('dashboard_holding', absolute: false));
    }
    public function store_user(LoginRequest $request): RedirectResponse
    {
        // dd($request->all());
        $request->authenticate();

        $request->session()->regenerate();
        $authUser = Auth::user();
        // dd($authUser);
        if ($authUser->user_aktif === 'NON AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Kita harus melemparkan ValidationException lagi
            // Tapi karena di Controller, kita bisa menggunakan return back() dengan error spesifik
            $request->session()->flash('user_nonaktif', 'Akun Anda non-aktif. Silakan hubungi administrator.');
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda non-aktif. Silakan hubungi administrator.',
            ]);
        }
        if ($authUser->is_admin === 'user') {
            $karyawan = Karyawan::where('nomor_identitas_karyawan', $authUser->karyawan_id)->first();

            // Cek Karyawan Null atau Non Aktif
            if (!$karyawan || $karyawan->status_aktif === 'NON AKTIF') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = !$karyawan
                    ? 'Data karyawan tidak ditemukan.'
                    : 'Status karyawan Anda non-aktif.';
                ActivityLog::create([
                    'user_id' => $authUser->id,
                    'user_name' => $authUser->username,
                    'menu_name' => 'login',
                    'id_table' => $authUser->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'users',
                    'activity' => 'auth',
                    'karir' => '2',
                    'description' => 'gagal login karena karyawan non aktif (mobile)',
                    'keterangan' => 'Failed',

                ]);
                $request->session()->flash('user_nonaktif', 'Akun Anda non-aktif. Silakan hubungi administrator.');
                return redirect()->route('login')->withErrors(['username' => $errorMessage]);
            }

            // Alert::success('Berhasil', 'Selamat Datang');
            $home = Menu::where('url', 'home')->where('kategori', 'mobile')->where('main', 2)->where('is_active', 1)->first();
            if ($authUser->menu_active == null) {
                User::where('id', $authUser->id)->update(['menu_active' => $home->id]);
                ActivityLog::create([
                    'user_id' => $authUser->id,
                    'user_name' => $authUser->username,
                    'menu_name' => 'login',
                    'id_table' => $authUser->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'users',
                    'activity' => 'auth',
                    'karir' => '2',
                    'description' => 'login menuju menu terakhir yang diakses (mobile)',
                    'keterangan' => 'success',

                ]);
                return redirect('/home/' . $home->id); // Redirect ke user home
            } else {
                $menu = Menu::where('id', $authUser->menu_active)->where('kategori', 'mobile')->where('main', 2)->where('is_active', 1)->first();
                if ($menu == null) {
                    ActivityLog::create([
                        'user_id' => $authUser->id,
                        'user_name' => $authUser->username,
                        'menu_name' => 'login',
                        'id_table' => $authUser->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'users',
                        'activity' => 'auth',
                        'karir' => '2',
                        'description' => 'login menuju menu terakhir yang diakses (mobile)',
                        'keterangan' => 'success',

                    ]);
                    User::where('id', $authUser->id)->update(['menu_active' => $home->id]);
                    return redirect('/home/' . $home->id);
                } else {
                    ActivityLog::create([
                        'user_id' => $authUser->id,
                        'user_name' => $authUser->username,
                        'menu_name' => 'login',
                        'id_table' => $authUser->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'users',
                        'activity' => 'auth',
                        'karir' => '2',
                        'description' => 'login berhasil (mobile)',
                        'keterangan' => 'success',

                    ]);
                    return redirect('/' . $menu->url . '/' . $menu->id); // Redirect ke user home
                }
            }
        } else {
            Auth::logout();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flash('user_nonaktif', 'Akun Anda non-aktif. Silakan hubungi administrator.');
            return redirect()->route('login_user')->withErrors([
                'username' => 'Akun Anda Bukan User. Silakan hubungi HRD.',
            ]);
        }
        // dd($authUser->username);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $ssoIdToken = $request->session()->get('sso.id_token');
        $authUser = Auth::guard('web');
        // dd($authUser->user());
        if ($authUser->user()->menu_active != null) {
            User::where('id', $authUser->user()->id)->update(['menu_active' => NULL]);
        }
        ActivityLog::create([
            'user_id' => $authUser->user()->id,
            'user_name' => $authUser->user()->username,
            'menu_name' => 'logout',
            'id_table' => $authUser->user()->id,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'users',
            'activity' => 'auth',
            'description' => 'Logout (PC/Komputer)',
            'keterangan' => 'Success',
        ]);
        $authUser->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        $request->session()->flash('logout_success', true);
        if ($ssoIdToken) {
            return redirect()->away(app(\App\Services\KeycloakClient::class)->logoutUrl($ssoIdToken));
        }
        return redirect()->route('login');
    }
    public function destroy_user(Request $request): RedirectResponse
    {
        $ssoIdToken = $request->session()->get('sso.id_token');
        $authUser = Auth::guard('web');
        // dd($authUser->user());
        if ($authUser->user()->menu_active != null) {
            User::where('id', $authUser->user()->id)->update(['menu_active' => NULL]);
        }
        ActivityLog::create([
            'user_id' => $authUser->user()->id,
            'user_name' => $authUser->user()->username,
            'menu_name' => 'logout',
            'id_table' => $authUser->user()->id,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'users',
            'activity' => 'auth',
            'karir' => '2',
            'description' => 'Logout (Mobile)',
            'keterangan' => 'Success',
        ]);
        $authUser->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        $request->session()->flash('logout_user_success', 'Logout Berhasil');
        if ($ssoIdToken) {
            return redirect()->away(app(\App\Services\KeycloakClient::class)->logoutUrl($ssoIdToken));
        }
        return redirect()->route('login_user');
    }
}
