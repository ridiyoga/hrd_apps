<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                // 1. Tentukan tujuan redirect berdasarkan peran atau guard
                // $redirectTo = '/dashboard/holding';
                // dd("Guard aktif: {$guard}. Seharusnya redirect ke dashboard/holding.");
                // Jika guard 'web' aktif, Anda bisa mendapatkan user dan mengecek peran:
                if ($guard === 'web') {
                    $user = Auth::guard('web')->user();

                    // Logika Redirect Multi-Role:
                    if ($user && $user->is_admin === 'user') {
                        if ($user->menu_active == null) {
                            $menu = Menu::where('url', 'home')->where('kategori', 'mobile')->where('main', 2)->where('is_active', 1)->first();
                            if ($menu) {
                                $redirectTo = '/' . $menu->url . '/' . $menu->id;
                            }
                        } else {
                            $menu = Menu::where('id', $user->menu_active)->where('kategori', 'mobile')->where('main', 2)->where('is_active', 1)->first();
                            if ($menu) {
                                $redirectTo = '/' . $menu->url . '/' . $menu->id;
                            }
                        }
                    } else {
                        $redirectTo = '/dashboard/holding'; // Redirect admin/default ke /dashboard/holding
                    }
                }

                // 2. Beri Response berdasarkan jenis request (Web vs API)
                if ($request->expectsJson()) {
                    // Jika request adalah API (misalnya dari Axios, Fetch)
                    // Beri response JSON 409 Conflict (Sudah terautentikasi)
                    return response()->json([
                        'message' => 'Anda sudah terautentikasi.',
                        'redirect' => $redirectTo // Beri tahu frontend tujuan redirect
                    ], 409);
                }

                // Jika request adalah request web biasa (bukan JSON)
                return redirect($redirectTo);
            }
        }

        return $next($request);
    }
}
