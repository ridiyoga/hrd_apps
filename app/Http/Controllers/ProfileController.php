<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        // dd($get_role);
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $menus = collect();
        } else {
            $menus = Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
                ->whereNull('parent_id') // menu utama
                ->with('children')
                ->where('kategori', 'web')      // load submenunya
                ->orderBy('sort_order')
                ->get();
        }
        return view('admin.profile.index', [
            'menus' => $menus,
            'holding' => $holding
        ]);
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
    public function save_profile(Request $request)
    {
        $rules = [
            'photo'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'username'  => 'required|max:255|unique:users,username,' . Auth::user()->id . ',id',
            'password'  => [
                'required_if:change_password,1',
                'confirmed',
                Password::min(6)->mixedCase()->numbers()
            ],

        ];
        $customMessages = [
            'password.min'      => 'Password minimal 6 karakter',
            'password.letters'  => 'Password harus mengandung huruf',
            'password.numbers'  => 'Password harus mengandung angka',
            'password.mixed'    => 'Password harus mengandung huruf dan angka',
            'password.mixedCase'    => 'Password harus mengandung huruf besar dan kecil',
            'username.required' => 'Username Tidak Boleh Kosong',
            'password.confirmed' => 'Konfirmasi Password Tidak Cocok',
            'photo.required' => 'Foto Tidak Boleh Kosong',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format File Harus jpeg,png,jpg,gif,svg',
            'photo.max' => 'Ukuran File Maksimal 2MB',
        ];
        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ]);
        }
        try {
            $file = $request->file('photo');
            if ($file != null) {
                $file = $request->file('photo')->store('profile');
                $photo = basename($file);
            }

            $user = User::where('id', Auth::user()->id)->first();
            if ($user == null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'User Tidak Ditemukan'
                ]);
            }
            $user->photo = $photo;
            $user->username = $request->username;
            if ($request->change_password == 1) {
                $user->password = Hash::make($request->password);
            }
            $user->password_show = $request->password;
            $user->save();
            return response()->json([
                'code' => 200,
                'message' => 'Success',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
