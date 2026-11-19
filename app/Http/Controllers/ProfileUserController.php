<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use App\Models\Cities;
use App\Models\District;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KaryawanKontrak;
use App\Models\KaryawanPendidikan;
use App\Models\KaryawanTemp;
use App\Models\KaryawanTempAlamat;
use App\Models\Menu;
use App\Models\Provincies;
use App\Models\Regencies;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\StrukturModel;
use App\Models\Village;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Intervention\Image\Laravel\Facades\Image;

class ProfileUserController extends Controller
{
    public function index($menu_id)
    {
        // dd(Karyawan::where('id', Auth::user()->karyawan_id)->first());
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        $user_karyawan = Karyawan::with('PenempatanKerja')->where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', Auth::user()->karyawan_id)->where('main', 1)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        return view('users.profile.index', [
            'menu_id' => $menu_id,
            'title' => 'Profile',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'karyawan_jabatan' => $karyawan_jabatan
        ]);
    }
    public function change_photoprofile_camera($menu_id)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        return view('users.profile.change_photoprofile_camera', [
            'title' => 'Profile',
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id,
            'user_karyawan' => $user_karyawan
        ]);
    }
    public function save_capture_profile(Request $request)
    {
        // 🔐 Validasi awal
        // dd($request->all());
        $customMessages = [
            'required' => 'Foto tidak ditemukan, silakan ambil ulang.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'mimes' => ':attribute harus berformat JPG, JPEG, PNG',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'foto_karyawan_pic' => 'required|image|mimes:jpg,jpeg,png|max:5000'
        ];


        try {
            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile', $validator->errors()->first());
            }
            // Update menu active
            // dd($request->all());
            // $karyawan = Karyawan::findOrFail(Auth::user()->karyawan_id);

            // // 📷 Ambil base64 image
            // $base64Image = $request->gallery_image;

            // if (!Str::contains($base64Image, 'base64,')) {
            //     $request->session()->flash('photo_update_error', 'Format gambar tidak valid');
            //     return back();
            // }
            // // Decode base64
            // [$meta, $content] = explode(',', $base64Image);
            // $imageData = base64_decode($content);

            // // 📁 Nama file
            // $fileName = now()->format('Ymd') . '-' . Uuid::uuid4() . '.jpg';
            // $path = "foto_karyawan/{$fileName}";
            // $path_old = "foto_karyawan/";

            // // 🖼️ Olah gambar (square, profesional)
            // $image = Image::read($imageData)
            //     ->cover(400, 400) // crop tengah, hasil konsisten
            //     ->toJpeg(85);

            // // 💾 Simpan ke storage
            // Storage::disk('public')->put($path, $image);
            // $old_gallery = $request->old_gallery;
            // if ($old_gallery != null) {
            //     Storage::disk('public')->delete($path_old . $old_gallery);
            // }
            // // dd($fileName);
            // // 🧾 Update DB
            // $karyawan->update([
            //     'foto_karyawan' => $fileName
            // ]);
            if ($request->foto_karyawan_pic != null) {
                if ($request->foto_karyawan_old != null) {
                    if (Storage::disk('foto_karyawan')->exists($request->foto_karyawan_old)) {
                        Storage::disk('foto_karyawan')->delete($request->foto_karyawan_old);
                    }
                }
                $foto_karyawan = $request->file('foto_karyawan_pic')->store('foto_karyawan');
                $foto_karyawan_save = basename($foto_karyawan);
            } else {
                $foto_karyawan_save = $request->foto_karyawan_old;
            }

            // dd($foto_karyawan_save);
            Karyawan::where('id', $request->karyawan_id)->update([
                'foto_karyawan' => $foto_karyawan_save
            ]);
            $karyawan_id = Karyawan::where('id', $request->karyawan_id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->karyawan_id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'Mengupdate foto profil ' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $request->session()->flash('photo_update_success', 'Foto profil berhasil diperbarui');
            return redirect('profile/' . Auth::user()->menu_active)->with('photo_update_success', 'Foto profil berhasil diperbarui');
        } catch (\Throwable $e) {

            // Logging internal (penting untuk IT)
            Log::error('Gagal upload foto profil', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage()
            ]);
            $request->session()->flash('photo_update_error', $e->getMessage());
            return back()->with(
                'profile_update_error',
                'Gagal menyimpan foto. Silakan coba lagi atau hubungi IT.'
            );
        }
    }
    public function detail_profile($menu_id)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $provinsi = Provincies::orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $kabupaten = Cities::where('province_code', $user_karyawan->provinsi)->orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $kecamatan = District::where('city_code', $user_karyawan->kabupaten)->orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $desa = Village::where('district_code', $user_karyawan->kecamatan)->orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $provinsi_domisili = Provincies::orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $kabupaten_domisili = Cities::where('province_code', $user_karyawan->provinsi_domisili)->orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $kecamatan_domisili = District::where('city_code', $user_karyawan->kabupaten_domisili)->orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        $desa_domisili = Village::where('district_code', $user_karyawan->kecamatan_domisili)->orderBy('name', 'ASC')->pluck('name', 'code')->toArray();
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        $pendidikan = KaryawanPendidikan::where('id_karyawan', $user_karyawan->id)->get();
        $karyawan_temp_null = KaryawanTemp::where('id_karyawan', $user_karyawan->id)->whereNull('status')->get();
        $karyawan_temp = KaryawanTemp::where('id_karyawan', $user_karyawan->id)->get();
        $karyawan_temp_jml = KaryawanTemp::where('id_karyawan', $user_karyawan->id)->whereNull('status')->count();
        $karyawan_temp_alamat_null = KaryawanTempAlamat::where('id_karyawan', $user_karyawan->id)->whereNull('status')->get();
        $karyawan_temp_alamat = KaryawanTempAlamat::where('id_karyawan', $user_karyawan->id)->get();
        $karyawan_temp_alamat_jml = KaryawanTempAlamat::where('id_karyawan', $user_karyawan->id)->whereNull('status')->count();
        return view('users.profile.detail_profile', [
            'title' => 'Profile',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'kecamatan' => $kecamatan,
            'desa' => $desa,
            'provinsi_domisili' => $provinsi_domisili,
            'kabupaten_domisili' => $kabupaten_domisili,
            'kecamatan_domisili' => $kecamatan_domisili,
            'desa_domisili' => $desa_domisili,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id,
            'pendidikan' => $pendidikan,
            'karyawan_temp_null' => $karyawan_temp_null,
            'karyawan_temp' => $karyawan_temp,
            'karyawan_temp_jml' => $karyawan_temp_jml,
            'karyawan_temp_alamat_null' => $karyawan_temp_alamat_null,
            'karyawan_temp_alamat' => $karyawan_temp_alamat,
            'karyawan_temp_alamat_jml' => $karyawan_temp_alamat_jml,

        ]);
    }
    public function get_kabupaten($id)
    {
        try {
            $kabupaten = Cities::where('province_code', $id)->orderBy('name', 'ASC')->get()->toArray();
            return response()->json([
                'code' => 200,
                'data' => $kabupaten
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'data' => $th->getMessage()
            ]);
        }
    }
    public function get_kecamatan($id)
    {
        try {
            $kecamatan = District::where('city_code', $id)->orderBy('name', 'ASC')->get()->toArray();
            return response()->json([
                'code' => 200,
                'data' => $kecamatan
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'data' => $th->getMessage()
            ]);
        }
    }
    public function get_desa($id)
    {
        try {
            $desa = Village::where('district_code', $id)->orderBy('name', 'ASC')->get()->toArray();
            return response()->json([
                'code' => 200,
                'data' => $desa
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'data' => $th->getMessage()
            ]);
        }
    }
    public function save_detail_profile(Request $request)
    {
        // dd($request->all());
        if ($request->kk_old == NULL) {
            $kk_val = 'required|image|mimes:jpg,jpeg,png|max:5000';
        } elseif ($request->kk_old != NULL) {
            $kk_val = 'nullable|image|mimes:jpg,jpeg,png|max:5000';
        }
        if ($request->ktp_old == NULL) {
            $ktp_val = 'required|image|mimes:jpg,jpeg,png|max:5000';
        } elseif ($request->ktp_old != NULL) {
            $ktp_val = 'nullable|image|mimes:jpg,jpeg,png|max:5000';
        }
        // dd($kk_val, $ktp_val);
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'mimes' => ':attribute harus berformat JPG, JPEG, PNG',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'name'           => 'required|max:255',
            'nik'            => 'required|max:16|unique:karyawans,nik,' . $request->karyawan_id,
            'email'          => 'nullable|email:rfc,dns|max:255|unique:karyawans,email,' . $request->karyawan_id,
            'no_kk'          => 'required|max:16',
            'kk_pic'         => $kk_val,
            'ktp_pic'        => $ktp_val,
            'tgl_terbit_kk'  => 'required',
            'telepon'        => 'nullable|min:11|max:13',
            'status_nomor'   => 'nullable|in:ya,tidak',
            'nomor_wa'       => ($request->status_nomor === 'ya')
                ? 'nullable|min:11|max:13'
                : 'required|min:11|max:13',
            'tempat_lahir'   => 'required|max:255',
            'tgl_lahir'      => 'required|date',
            'agama'          => 'required',
            'gender'         => 'required',
            'status_nikah'   => 'required',
            'alasan'         => 'required',
            'jumlah_anak'    => 'nullable|integer|min:0',
        ];
        try {
            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile_update_error', $validator->errors()->first());
            }
            $data = $validator->validated();

            $kk_save = $request->kk_old;
            if ($request->hasFile('kk_pic')) {
                $kk = $request->file('kk_pic')->store('', 'kk');
                if ($kk === false) {
                    throw new \RuntimeException('Gagal menyimpan file Kartu Keluarga.');
                }
                $kk_save = basename($kk);
            }

            $ktp_save = $request->ktp_old;
            if ($request->hasFile('ktp_pic')) {
                $ktp = $request->file('ktp_pic')->store('', 'ktp');
                if ($ktp === false) {
                    throw new \RuntimeException('Gagal menyimpan file KTP.');
                }
                $ktp_save = basename($ktp);
            }

            // dd($data);
            $id_temp = Uuid::uuid4();
            $karyawan_id = Karyawan::where('id', $request->karyawan_id)->first();
            // dd($karyawan_id->kontrak_kerja);
            KaryawanTemp::create([
                'kk' => $kk_save,
                'ktp' => $ktp_save,

                'id' => $id_temp,
                'id_karyawan' => $request->karyawan_id,
                'name' => strtoupper($request->name),
                'nik' => $request->nik,
                'email' => $request->email,
                'no_kk' => $request->no_kk,
                'tgl_terbit_kk' => $request->tgl_terbit_kk,
                'telepon' => $request->telepon,
                'status_nomor' => $request->status_nomor,
                'nomor_wa' => $request->nomor_wa,
                'tempat_lahir' => strtoupper($request->tempat_lahir),
                'tgl_lahir' => $request->tgl_lahir,
                'agama' => $request->agama,
                'status_nikah' => $request->status_nikah,
                'gender' => $request->status_nikah,
                'jumlah_anak' => $request->jumlah_anak,
                'alasan' => strtoupper($request->alasan),
                'holding' => $karyawan_id->kontrak_kerja,
            ]);

            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_temp,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_temp',
                'activity' => 'create',
                'description' => 'Membuat pengajuan update profil ' . strtoupper($request->name),
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $request->session()->flash('profile_update_success', 'Pengajuan pembaharuan profil berhasil');
            return redirect()->back()->with('profile_update_success', 'Pengajuan pembaharuan profil berhasil');
        } catch (ValidationException $e) {
            $request->session()->flash('profile_update_error', $e->validator->errors()->first());
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('profile_update_error', $e->validator->errors()->first());
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }

    public function save_pendidikan(Request $request)
    {
        // dd($request->all());
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'mimes' => ':attribute harus berformat JPG, JPEG, PNG',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'id_karyawan' => 'required',
            'institusi' => 'required',
            'jurusan' => 'required',
            'jenjang' => 'required',
            'tanggal_masuk' => 'required',
            'tanggal_keluar' => 'required',
        ];
        try {

            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile_update_error', $validator->errors()->first());
            }
            $id_pendidikan = Uuid::uuid4();
            KaryawanPendidikan::create(
                [
                    'id_pendidikan' => $id_pendidikan,
                    'id_karyawan' => $request->id_karyawan,
                    'institusi' => strtoupper($request->institusi),
                    'jurusan' => strtoupper($request->jurusan),
                    'jenjang' => $request->jenjang,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'tanggal_keluar' => $request->tanggal_keluar,
                    'created_at' => now(),
                ]
            );
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_pendidikan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_pendidikan',
                'activity' => 'create',
                'description' => 'Menambah data pendidikan' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $request->session()->flash('profile_update_success', 'Pendidikan berhasil diperbarui');
            return redirect()->back()->with('profile_update_success', 'Pendidikan berhasil diperbarui');
        } catch (ValidationException $e) {
            $request->session()->flash('profile_update_error', $e->validator->errors()->first());
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('profile_update_error', $e->validator->errors()->first());
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function update_pendidikan(Request $request)
    {
        // dd($request->all());
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'mimes' => ':attribute harus berformat JPG, JPEG, PNG',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'id_karyawan' => 'required',
            'institusi' => 'required',
            'jurusan' => 'required',
            'jenjang' => 'required',
            'tanggal_masuk' => 'required',
            'tanggal_keluar' => 'required',
        ];
        try {

            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile_update_error', $validator->errors()->first());
            }
            KaryawanPendidikan::where('id_pendidikan', $request->id_pendidikan)->update(
                [
                    'id_karyawan' => $request->id_karyawan,
                    'institusi' => strtoupper($request->institusi),
                    'jurusan' => strtoupper($request->jurusan),
                    'jenjang' => $request->jenjang,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'tanggal_keluar' => $request->tanggal_keluar,
                    'updated_at' => now(),
                ]
            );
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_pendidikan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_pendidikan',
                'activity' => 'update',
                'description' => 'mengedit data pendidikan ' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $request->session()->flash('profile_update_success', 'Pendidikan berhasil diperbarui');
            return redirect()->back()->with('profile_update_success', 'Pendidikan berhasil diperbarui');
        } catch (ValidationException $e) {
            $request->session()->flash('profile_update_error', $e->validator->errors()->first());
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('profile_update_error', $e->validator->errors()->first());
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function delete_pendidikan(Request $request)
    {
        try {
            $get = KaryawanPendidikan::where('id_pendidikan', $request->id_pendidikan);
            $get_pendidikan = KaryawanPendidikan::where('id_pendidikan', $request->id_pendidikan)->first();
            $karyawan_id = Karyawan::where('id', $get_pendidikan->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_pendidikan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_pendidikan',
                'activity' => 'delete',
                'description' => 'menghapus data pendidikan ' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $get->delete();
            $request->session()->flash('profile_update_success', 'Pendidikan berhasil dihapus');
            return redirect()->back()->with('profile_update_success', 'Pendidikan berhasil dihapus');
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function dokumen_pendidikan(Request $request)
    {
        // dd($request->all());
        if ($request->ijazah_old == NULL) {
            $ijazah_val = 'required|mimes:pdf|max:5000';
        } elseif ($request->ijazah_old != NULL) {
            $ijazah_val = 'nullable';
        }
        if ($request->transkrip_nilai_old == NULL) {
            $transkrip_nilai_val = 'required|mimes:pdf|max:5000';
        } elseif ($request->transkrip_nilai_old != NULL) {
            $transkrip_nilai_val = 'nullable';
        }
        // dd($ijazah_val, $transkrip_nilai_val);
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'mimes' => ':attribute harus berformat pdf',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'ijazah'            => $ijazah_val,
            'transkrip_nilai'   => $transkrip_nilai_val,
            'ipk'               => 'required',
        ];
        try {
            if ($request->ijazah != null) {
                if ($request->ijazah_old != null) {
                    if (Storage::disk('ijazah')->exists($request->ijazah_old)) {
                        Storage::disk('ijazah')->delete($request->ijazah_old);
                    }
                }
                $ijazah = $request->file('ijazah')->store('ijazah');
                $ijazah_save = basename($ijazah);
            } else {
                $ijazah_save = $request->ijazah_old;
            }
            if ($request->transkrip_nilai != null) {
                if ($request->transkrip_nilai_old != null) {
                    if (Storage::disk('transkrip_nilai')->exists($request->transkrip_nilai_old)) {
                        Storage::disk('transkrip_nilai')->delete($request->transkrip_nilai_old);
                    }
                }
                $transkrip_nilai = $request->file('transkrip_nilai')->store('transkrip_nilai');
                $transkrip_nilai_save = basename($transkrip_nilai);
            } else {
                $transkrip_nilai_save = $request->transkrip_nilai_old;
            }


            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile_update_error', $validator->errors()->first());
            }
            $data = $validator->validated();
            // dd($ijazah_save, $transkrip_nilai_save);
            // dd(Karyawan::where('id', $request->id_karyawan)->first());

            Karyawan::where('id', $request->id_karyawan)->update([
                'ijazah'            => $ijazah_save,
                'transkrip_nilai'   => $transkrip_nilai_save,
                'ipk'               => $request->ipk,
            ]);
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->karyawan_id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'Mengupdate dokumen pendidikan ' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $request->session()->flash('profile_update_success', 'Dokumen Pendidikan berhasil diperbarui');
            return redirect()->back()->with('profile_update_success', 'Dokumen Pendidikan berhasil diperbarui');
        } catch (ValidationException $e) {
            $request->session()->flash('profile_update_error', $e->validator->errors()->first());
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('profile_update_error', $e->validator->errors()->first());
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }

    public function download_ijazah($file)
    {
        $path = public_path('storage/ijazah/' . $file);
        // dd($path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
    public function download_transkrip_nilai($file)
    {
        $path = public_path('storage/transkrip_nilai/' . $file);
        // dd($path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
    public function delete_profil(Request $request)
    {
        try {
            if ($request->ktp != NULL) {

                Storage::disk('ktp')->delete($request->ktp);
            }
            if ($request->kk != NULL) {

                Storage::disk('kk')->delete($request->kk);
            }
            $get = KaryawanTemp::where('id', $request->id);
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_temp',
                'activity' => 'Delete',
                'description' => 'Menghapus pengajuan update profil ' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $get->delete();
            $request->session()->flash('profile_update_success', 'Pengajuan berhasil dihapus');
            return redirect()->back()->with('profile_update_success', 'Pengajuan berhasil dihapus');
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function delete_alamat(Request $request)
    {
        try {
            $get = KaryawanTempAlamat::where('id', $request->id);
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_temp',
                'activity' => 'Delete',
                'description' => 'Menghapus pengajuan update Alamat ' . $karyawan_id->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $get->delete();
            $request->session()->flash('profile_update_success', 'Pengajuan berhasil dihapus');
            return redirect()->back()->with('profile_update_success', 'Pengajuan berhasil dihapus');
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function detail_hr($menu_id)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        return view('users.profile.detail_hr', [
            'title' => 'Profile',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id,
        ]);
    }
    public function save_detail_alamat(Request $request)
    {
        // dd($request->all());

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'rt' => 'required|max:255',
            'rw' => 'required|max:255',
            'kode_pos' => 'required|max:255',
            'detail_alamat' => 'required|max:255',
            'status_alamat' => 'nullable|max:11',
            'provinsi_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'kabupaten_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'kecamatan_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'desa_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'rt_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'rw_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'kode_pos_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'detail_alamat_domisili' => $request->status_alamat === 'tidak' ? 'required' : 'nullable',
            'alasan' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules, $customMessages);

            // dd($validator->fails());
            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile_update_error', $validator->errors()->first());
            }
            $karyawan = Karyawan::where('id', $request->karyawan_id)->first();
            $query                                        = new KaryawanTempAlamat();
            // dd($query);
            $query->id                                    = Uuid::uuid4();
            $query->id_karyawan                           = $request->karyawan_id;
            $query->provinsi                              = $request->provinsi;
            $query->kabupaten                             = $request->kabupaten;
            $query->kecamatan                             = $request->kecamatan;
            $query->desa                                  = $request->desa;
            $query->rt                                    = $request->rt;
            $query->rw                                    = $request->rw;
            $query->kode_pos                              = $request->kode_pos;
            $query->detail_alamat                         = strtoupper($request->detail_alamat);
            $query->alamat                                = $request->detail_alamat . ' ' . $request->desa . ', ' . $request->kecamatan . ', ' . $request->kabupaten . ', ' . $request->provinsi;
            $query->status_alamat                         = $request->status_alamat;
            $query->provinsi_domisili                     = $request->provinsi_domisili;
            $query->kabupaten_domisili                    = $request->kabupaten_domisili;
            $query->kecamatan_domisili                    = $request->kecamatan_domisili;
            $query->desa_domisili                         = $request->desa_domisili;
            $query->detail_alamat_domisili                = strtoupper($request->detail_alamat_domisili);
            $query->rt_domisili                           = $request->rt_domisili;
            $query->rw_domisili                           = $request->rw_domisili;
            $query->kode_pos_domisili                     = $request->kode_pos_domisili;
            $query->alamat_domisili                       = $request->detail_alamat_domisili . ' ' . $request->desa_domisili . ', ' . $request->kecamatan_domisili . ', ' . $request->kabupaten_domisili . ', ' . $request->provinsi_domisili;
            $query->alasan                                = strtoupper($request->alasan);
            $query->holding                               = $karyawan->kontrak_kerja;

            $query->save();

            $holdings = Holding::where('id', $karyawan->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_temp_alamat',
                'activity' => 'create',
                'description' => 'membuat pengajuan update alamat ' . $karyawan->name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            // return $query;
            $request->session()->flash('profile_update_success', 'Pengajuan ubah alamat berhasil');
            return redirect()->back()->with('profile_update_success', 'Pengajuan ubah alamat berhasil');
        } catch (ValidationException $e) {
            $request->session()->flash('profile_update_error', $e->validator->errors()->first());
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('profile_update_error', $e->validator->errors()->first());
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function save_detail_kontak_kantor(Request $request)
    {
        // dd($request->all());

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $rules = [
            'email_kantor' => 'max:255|email|nullable',
            'nomor_wa_kantor' => 'max:255|min:10|nullable',
        ];

        try {
            $validator = Validator::make($request->all(), $rules, $customMessages);

            // dd($validator->fails());
            if ($validator->fails()) {
                $request->session()->flash('profile_update_error', $validator->errors()->first());
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('profile_update_error', $validator->errors()->first());
            }
            // dd($request->all());
            $update_kontak_kantor = Karyawan::where('id', $request->karyawan_id)->first();
            $update_kontak_kantor->email_kantor                          = $request->email_kantor;
            $update_kontak_kantor->nomor_wa_kantor                       = $request->nomor_wa_kantor;
            $update_kontak_kantor->save();

            $holdings = Holding::where('id', $update_kontak_kantor->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'Karyawans',
                'activity' => 'create',
                'description' => 'Meanmahkan kontak kantor ' . $update_kontak_kantor->email_kantor . ' dan ' . $update_kontak_kantor->nomor_wa_kantor,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            // return $query;
            $request->session()->flash('profile_update_success', 'Penambahan Kontak Kantor berhasil');
            return redirect()->back()->with('profile_update_success', 'Penambahan Kontak Kantor berhasil');
        } catch (ValidationException $e) {
            $request->session()->flash('profile_update_error', $e->validator->errors()->first());
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('profile_update_error', $e->validator->errors()->first());
        } catch (\Throwable $e) {
            $request->session()->flash('profile_update_error', $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('profile_update_error', $e->getMessage());
        }
    }
    public function update_username(Request $request)
    {
        // dd($request->all());

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
        ];
        $credentials = $request->validate([
            'username' => 'required',
        ], $customMessages);
        $query                                        = User::where('id', $request->user_id)->first();
        // dd($query);
        $query->username                              = $credentials['username'];
        $query->update();
        if ($query) {
            return back()->with('profile_update_success', 'Username berhasil diperbarui.');
        } else {
            return back()->withErrors('profile_update_error', 'Username gagal diperbarui.');
        }
    }
    public function save_detail_account(Request $request)
    {
        // dd($request->all());

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required|min:8|',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
        ], $customMessages);
        $query                                        = User::where('id', $request->user_id)->first();
        $query->username                              = $credentials['username'];
        $query->password                              = Hash::make($credentials['password']);
        $query->password_show                         = $credentials['password'];
        $query->update();
        if ($query) {
            $request->session()->flash('profile_update_success');
        } else {
            $request->session()->flash('profile_update_error');
        }
        return redirect('profile');
    }
    public function detail_account($menu_id)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.profile.detail_user', [
            'user_karyawan' => $user_karyawan,
            'title' => 'Profile',
            'all_menu_bar' => $all_menu_bar,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'menu_id' => $menu_id
        ]);
    }
    public function change_password($menu_id)
    {
        // dd(Auth::user()->id);
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        // dd($user_karyawan);
        return view('users.profile.change_password', [
            'title' => 'Profile',
            'user_karyawan' => $user_karyawan,
            'all_menu_bar' => $all_menu_bar,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'menu_id' => $menu_id
        ]);
    }
    public function save_change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()      // harus ada huruf
                    ->mixedCase()    // huruf besar & kecil
                    ->numbers()      // harus ada angka
            ],
        ], [
            'old_password.required' => 'Password lama wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            // dd($validator);
            // Hapus flash session agar tidak bentrok dengan SweetAlert
            return back()
                ->withErrors($validator)
                ->withInput($request->all()); // ⬅️ Kunci agar password tetap terisi
        }

        $user = User::where('id', Auth::user()->id)->first();

        // 🔐 Check old password
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors([
                'old_password' => 'Password lama tidak sesuai'
            ])->withInput($request->all()); // ⬅️ Tambahkan ini juga
        }

        // 🚫 Cegah password sama
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors([
                'new_password' => 'Password baru tidak boleh sama dengan password lama'
            ])->withInput($request->all()); // ⬅️ Tambahkan ini juga
        }

        try {
            // ✅ Update password
            $user->update([
                'password' => Hash::make($request->new_password),
                'password_show' => $request->new_password
            ]);

            $user_name = User::where('id', $request->user()->id)->first();
            $karyawan_id = Karyawan::where('nomor_identitas_karyawan', $user_name->karyawan_id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->user()->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'users',
                'activity' => 'update',
                'description' => 'Mengubah Password',
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $request->session()->flash('password_update_success', 'Password berhasil diperbarui');
            return back()->with('success', 'Password berhasil diperbarui');
        } catch (\Exception $e) {
            $request->session()->flash('password_update_error', $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui password: ' . $e->getMessage());
        }
    }
    public function lihat_jabatan($menu_id)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $karyawan_jabatan = karyawanJabatan::with(['Karyawan' => function ($query) {
            $query->where('status_aktif', 'AKTIF');
        }])
            ->with('Departemen')
            ->with('Divisi')
            ->with('Bagian')
            ->with('Jabatan')
            ->where('karyawan_id', $user_karyawan->id)
            ->first();
        // dd($user_karyawan->Karyawan->Departemen->nama_departemen);
        return view('users.profile.lihat_jabatan', [
            'title' => 'Profile',
            'karyawan_jabatan' => $karyawan_jabatan,
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menu_bar' => $all_menu_bar,
            'all_menus' => $all_menus,
            'menu_id' => $menu_id
        ]);
    }
    private function terbilang($angka)
    {
        $angka = abs((int) $angka);
        $bil = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

        if ($angka < 12) return $bil[$angka];
        if ($angka < 20) return $this->terbilang($angka - 10) . ' belas';
        if ($angka < 100) return $this->terbilang(intval($angka / 10)) . ' puluh ' . $this->terbilang($angka % 10);
        if ($angka < 200) return 'seratus ' . $this->terbilang($angka - 100);
        if ($angka < 1000) return $this->terbilang(intval($angka / 100)) . ' ratus ' . $this->terbilang($angka % 100);
        if ($angka < 2000) return 'seribu ' . $this->terbilang($angka - 1000);
        if ($angka < 1000000) return $this->terbilang(intval($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000);

        return '';
    }
    public function lihat_kontrak_kerja($menu_id)
    {
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $karyawan_kontrak = KaryawanKontrak::with('children')
            ->whereNull('parent_id')
            ->where('karyawan_id', $user_karyawan->id)
            ->orderBy('tanggal_mulai', 'asc')
            ->get();
        // dd($karyawan_kontrak);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.profile.lihat_kontrak', [
            'title' => 'Profile',
            'karyawan_kontrak' => $karyawan_kontrak,
            'all_menu_main' => $all_menu_main,
            'all_menu_bar' => $all_menu_bar,
            'all_menus' => $all_menus,
            'menu_id' => $menu_id,
            'user_karyawan' => $user_karyawan
        ]);
    }
    public function kontrak_download($id)
    {
        // dd($id);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $get_kontrak = KaryawanKontrak::where('id_kontrak', $id)
            ->with([
                'Karyawan' => function ($query) {
                    $query->with([
                        'provinsiKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kabupatenKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kecamatanKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'desaKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'provinsiNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kabupatenNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kecamatanNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'desaNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ]);
                }
            ])
            ->first();
        // dd($get_kontrak);
        $direktur = KaryawanJabatan::where('holding_id', $get_holding->id)->where('is_active', 1)->with([
            'Jabatan' => function ($query) {
                $query->with([
                    'LevelJabatan' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])->with([
            'Karyawan' => function ($query) {
                $query;
            }
        ])->whereHas('Jabatan.LevelJabatan', function ($query) {
            $query->where('level_jabatan', '-1');
        })
            ->where('main', '1')
            ->first();
        // dd($direktur);
        $jabatan = KaryawanJabatan::where('karyawan_id', $get_kontrak->Karyawan->id)->where('main', 1)->where('is_active', 1)->with([
            'Bagian' => function ($query) {
                $query->with([
                    'Divisi' => function ($query) {
                        $query->with([
                            'Departemen' => function ($query) {
                                $query->orderBy('nama_departemen', 'ASC');
                            }
                        ]);
                    }
                ]);
            }
        ])
            ->with('Jabatan.LevelJabatan')
            ->first();
        $hari_alf = Carbon::parse($get_kontrak->tanggal_mulai)->translatedFormat('l');
        $tanggal_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_mulai)->day);
        $bulan_alf = Carbon::parse($get_kontrak->tanggal_mulai)->translatedFormat('F');
        $tahun_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_mulai)->year);
        $date_mulai = Carbon::parse($get_kontrak->tanggal_mulai)->format('d-m-Y');
        $tanggal_mulai_kontrak = Carbon::parse($get_kontrak->tanggal_mulai)->format('d');
        $tanggal_mulai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_mulai)->format('d'));

        $tanggal_selesai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_selesai)->day);
        $bulan_selesai_alf = Carbon::parse($get_kontrak->tanggal_selesai)->translatedFormat('F');
        $tahun_selesai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_selesai)->year);
        $date_selesai_selesai = Carbon::parse($get_kontrak->tanggal_selesai)->format('d-m-Y');
        $tanggal_selesai_kontrak = Carbon::parse($get_kontrak->tanggal_selesai)->format('d');
        $tanggal_selesai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_selesai)->format('d'));

        $pdf = Pdf::setOption([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ])->loadView('admin.karyawan.kontrak_pdf', [
            'tittle' => 'Data Recruitment',
        ], compact(
            'get_holding',
            'jabatan',
            'get_kontrak',
            'hari_alf',
            'tanggal_alf',
            'bulan_alf',
            'tahun_alf',
            'date_mulai',
            'tanggal_mulai_kontrak',
            'tanggal_mulai_alf',

            'tanggal_selesai_alf',
            'bulan_selesai_alf',
            'tahun_selesai_alf',
            'tanggal_selesai_kontrak',
            'direktur',
        ));
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // TULIS FOOTER DI SINI
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            450,      // posisi X (kanan)
            820,      // posisi Y (bawah)
            "Halaman {PAGE_NUM} dari {PAGE_COUNT}",
            null,     // font default
            10,       // font size
            [0, 0, 0] // warna hitam
        );

        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas) use ($fontMetrics) {

            if ($pageNumber == $pageCount) return;

            $pageWidth  = $canvas->get_width();
            $pageHeight = $canvas->get_height();

            $cm = 28.35;

            /* =========================
       UKURAN RAMPING
    ========================= */
            $col       = 95;      // lebih kecil
            $headerH   = 16;
            $bodyH     = 38;
            $parafW    = $col * 2;
            $parafH    = $headerH + $bodyH;

            $x = $pageWidth  - (2.5 * $cm) - $parafW;
            $y = $pageHeight - (1   * $cm) - $parafH;

            $font = $fontMetrics->getFont("Times-Roman", "normal");
            $size = 7;

            $center = function ($text, $bx, $by, $bw, $bh) use ($canvas, $fontMetrics, $font, $size) {
                $tw = $fontMetrics->getTextWidth($text, $font, $size);
                $tx = $bx + ($bw - $tw) / 2;
                $ty = $by + ($bh / 2) - ($size / 2);
                $canvas->text($tx, $ty, $text, $font, $size);
            };

            /* HEADER KIRI */
            $canvas->rectangle($x, $y, $col, $headerH, [0, 0, 0], 1);
            $center("Paraf", $x, $y, $col, 8);
            $center("Karyawan", $x, $y + 8, $col, 8);

            /* HEADER KANAN */
            $rx = $x + $col;
            $canvas->rectangle($rx, $y, $col, $headerH, [0, 0, 0], 1);
            $center("Paraf Pihak", $rx, $y, $col, 8);
            $center("Kesatu", $rx, $y + 8, $col, 8);

            /* BODY */
            $canvas->rectangle($x,  $y + $headerH, $col, $bodyH, [0, 0, 0], 1);
            $canvas->rectangle($rx, $y + $headerH, $col, $bodyH, [0, 0, 0], 1);
        });
        return $pdf->stream('Kontrak ' . $get_kontrak->Karyawan->name . '.pdf');
    }
    public function lihat_dokumen()
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        return view('users.profile.lihat_dokumen', [
            'title' => 'Profile',
            'user_karyawan' => $user_karyawan
        ]);
    }
    public function lihat_rekan_kerja($menu_id)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $user_karyawan->id)->get();
        $holdingIds    = $karyawan_jabatan->pluck('holding_id')->toArray();
        $jabatanIds    = $karyawan_jabatan->pluck('jabatan_id')->toArray();

        // dd($rekan_kerja);
        $struktur = StrukturModel::with('UserKaryawan', 'ChildrenRecursive')->whereIn('jabatan', $jabatanIds)->whereIn('holding', $holdingIds)->get();
        $bawahanKaryawanIds = collectBawahan($struktur);
        $rekan_kerja = Karyawan::with(['KaryawanJabatan' => function ($q) {
            $q->where('is_active', 1);
            $q->where('main', 1);
            $q->with('Jabatan');
            $q->with('Departemen');
            $q->with('Bagian');
            $q->with('Divisi');
        }])->whereIn('id', $bawahanKaryawanIds)
            ->where('nomor_identitas_karyawan', '!=', Auth::user()->karyawan_id)
            ->where('status_aktif', 'AKTIF')
            ->orderBy('name', 'ASC')->get();
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.profile.lihat_rekan_kerja', [
            'title' => 'Profile',
            'rekan_kerja' => $rekan_kerja,
            'menu_id' => $menu_id,
            'all_menu_bar' => $all_menu_bar,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'user_karyawan' => $user_karyawan
        ]);
    }
    public function lihat_struktur_organisasi()
    {

        // syncfusion
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $user_site_job = Lokasi::where('lokasi_kantor', $user_karyawan->site_job)->first();
        if ($user_site_job->kategori_kantor == 'sp') {
            $user_site_job_karyawan =  ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'];
        } else if ($user_site_job->kategori_kantor == 'sps') {
            $user_site_job_karyawan =  ['ALL SITES (SP, SPS, SIP)', 'PT. SURYA PANGAN SEMESTA - KEDIRI', 'PT. SURYA PANGAN SEMESTA - NGAWI', 'PT. SURYA PANGAN SEMESTA - SUBANG', 'ALL SITES (SP)'];
        } else {
            $user_site_job_karyawan = ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'];
        }

        $jabatan_karyawan = Jabatan::With('Bagian')->with(['Divisi' => function ($query) {
            $query->With('Departemen');
        }])->where('id', $user_karyawan->jabatan_id)->first();
        if ($jabatan_karyawan == NULL) {
            $jabatan = NULL;
        } else {
            $jabatan = $jabatan_karyawan->atasan_id;
        }
        $jabatan_karyawan_atasan1 = Jabatan::With('Bagian')->with(['Divisi' => function ($query) {
            $query->With('Departemen');
        }])->where('id', $jabatan)->first();
        if ($jabatan_karyawan_atasan1 == NULL) {
            $jabatan_atasan1 = NULL;
        } else {
            $jabatan_atasan1 = $jabatan_karyawan_atasan1->atasan_id;
        }
        $jabatan_karyawan_atasan2 = Jabatan::With('Bagian')->with(['Divisi' => function ($query) {
            $query->With('Departemen');
        }])->where('id', $jabatan_atasan1)->first();
        if ($jabatan_karyawan_atasan2 == NULL) {
            $jabatan_atasan2 = NULL;
        } else {
            $jabatan_atasan2 = $jabatan_karyawan_atasan2->atasan_id;
        }
        $jabatan_karyawan_atasan3 = Jabatan::With('Bagian')->with(['Divisi' => function ($query) {
            $query->With('Departemen');
        }])->where('id', $jabatan_atasan2)->first();
        if ($jabatan_karyawan_atasan3 == NULL) {
            $jabatan_atasan3 = NULL;
        } else {
            $jabatan_atasan3 = $jabatan_karyawan_atasan3->atasan_id;
        }
        // dd($jabatan_karyawan_atasan3);
        $jabatan_karyawan_atasan4 = Jabatan::With('Bagian')->with(['Divisi' => function ($query) {
            $query->With('Departemen');
        }])->where('id', $jabatan_atasan3)->first();
        if ($jabatan_karyawan_atasan4 == NULL) {
            $jabatan_atasan4 = NULL;
        } else {
            $jabatan_atasan4 = $jabatan_karyawan_atasan4->atasan_id;
        }
        $jabatan_karyawan_atasan5 = Jabatan::With('Bagian')->with(['Divisi' => function ($query) {
            $query->With('Departemen');
        }])->where('id', $jabatan_atasan4)->first();
        $jabatan = [$jabatan_karyawan, $jabatan_karyawan_atasan1, $jabatan_karyawan_atasan2, $jabatan_karyawan_atasan3, $jabatan_karyawan_atasan4, $jabatan_karyawan_atasan5];

        // dd($jabatan);
        if (count($jabatan) == 0) {
            $jabatan_struktur = NULL;
        } else {
            foreach ($jabatan as $jabatan) {
                if ($jabatan == NULL) {
                    $jabatan_karyawan = NULL;
                    $jabatan_karyawan_id = NULL;
                    $jabatan_karyawan_atasan = NULL;
                    break;
                } else {
                    $jabatan_karyawan = $jabatan['nama_jabatan'];
                    $jabatan_karyawan_id = $jabatan['id'];
                    $jabatan_karyawan_atasan = $jabatan['atasan_id'];
                    if ($jabatan->Bagian == NULL) {
                        $jabatan_bagian = NULL;
                    } else {
                        $jabatan_bagian = $jabatan->Bagian["nama_bagian"];
                    }
                    if ($jabatan->Divisi == NULL) {
                        $jabatan_divisi = NULL;
                    } else {
                        $jabatan_divisi = $jabatan->Divisi["nama_divisi"];
                        if ($jabatan->Divisi->Departemen == NULL) {
                            $jabatan_departemen = NULL;
                        } else {
                            $jabatan_departemen = $jabatan->Divisi->Departemen["nama_departemen"];
                        }
                    }
                    // continue;
                }
                // dd($jabatan_departemen);
                // $ok = $jabatan->User->toArray();
                $ok = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan_id')
                    ->join('divisis', 'divisis.id', '=', 'karyawans.divisi_id')
                    ->join('departemens', 'departemens.id', '=', 'karyawans.dept_id')
                    ->join('bagians', 'bagians.id', '=', 'karyawans.bagian_id')
                    // ->whereIn('karyawans.site_job', ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'])
                    ->whereIn('karyawans.site_job', $user_site_job_karyawan)
                    ->where('departemens.nama_departemen', $jabatan_departemen)
                    ->where('divisis.nama_divisi', $jabatan_divisi)
                    ->where('bagians.nama_bagian', $jabatan_bagian)
                    ->where('jabatans.nama_jabatan', $jabatan_karyawan)
                    ->where('karyawans.status_aktif', 'AKTIF')
                    // ->orWhere('penempatan_kerja', 'ALL SITES (SP)')
                    // ->orWhere('karyawans.site_job', 'CV. SUMBER PANGAN - KEDIRI')
                    // ->take('5')
                    ->select('karyawans.name')
                    ->get()
                    ->toArray();
                $ok1 = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan1_id')
                    ->join('divisis', 'divisis.id', '=', 'karyawans.divisi1_id')
                    ->join('departemens', 'departemens.id', '=', 'karyawans.dept1_id')
                    ->join('bagians', 'bagians.id', '=', 'karyawans.bagian1_id')
                    // ->whereIn('karyawans.site_job', ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'])
                    ->whereIn('karyawans.site_job', $user_site_job_karyawan)
                    ->where('departemens.nama_departemen', $jabatan_departemen)
                    ->where('divisis.nama_divisi', $jabatan_divisi)
                    ->where('bagians.nama_bagian', $jabatan_bagian)
                    ->where('jabatans.nama_jabatan', $jabatan_karyawan)
                    ->where('karyawans.status_aktif', 'AKTIF')
                    // ->orWhere('karyawans.site_job', 'CV. SUMBER PANGAN - KEDIRI')
                    // ->orWhere('penempatan_kerja', 'ALL SITES (SP)')
                    // ->take('5')
                    ->select('karyawans.name')
                    ->get()
                    ->toArray();
                $ok2 = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan2_id')
                    ->join('divisis', 'divisis.id', '=', 'karyawans.divisi2_id')
                    ->join('departemens', 'departemens.id', '=', 'karyawans.dept2_id')
                    ->join('bagians', 'bagians.id', '=', 'karyawans.bagian2_id')
                    // ->whereIn('karyawans.site_job', ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'])
                    // ->where('divisis.nama_divisi', $jabatan["nama_divisi"])
                    // ->where('bagians.nama_bagian', $jabatan["nama_bagian"])
                    ->whereIn('karyawans.site_job', $user_site_job_karyawan)
                    ->where('departemens.nama_departemen', $jabatan_departemen)
                    ->where('divisis.nama_divisi', $jabatan_divisi)
                    ->where('bagians.nama_bagian', $jabatan_bagian)
                    ->where('jabatans.nama_jabatan', $jabatan_karyawan)
                    ->where('karyawans.status_aktif', 'AKTIF')
                    // ->where('karyawans.site_job', 'ALL SITES (SP, SPS, SIP)')
                    // ->orWhere('karyawans.site_job', 'CV. SUMBER PANGAN - KEDIRI')
                    // ->orWhere('penempatan_kerja', 'ALL SITES (SP)')
                    // ->take('5')
                    ->select('karyawans.name')
                    ->get()
                    ->toArray();
                $ok3 = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan3_id')
                    ->join('divisis', 'divisis.id', '=', 'karyawans.divisi3_id')
                    ->join('departemens', 'departemens.id', '=', 'karyawans.dept3_id')
                    ->join('bagians', 'bagians.id', '=', 'karyawans.bagian3_id')
                    // ->whereIn('karyawans.site_job', ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'])
                    // ->where('divisis.nama_divisi', $jabatan["nama_divisi"])
                    // ->where('bagians.nama_bagian', $jabatan["nama_bagian"])
                    ->whereIn('karyawans.site_job', $user_site_job_karyawan)
                    ->where('departemens.nama_departemen', $jabatan_departemen)
                    ->where('divisis.nama_divisi', $jabatan_divisi)
                    ->where('bagians.nama_bagian', $jabatan_bagian)
                    ->where('jabatans.nama_jabatan', $jabatan_karyawan)
                    ->where('karyawans.status_aktif', 'AKTIF')
                    // ->where('karyawans.site_job', 'ALL SITES (SP, SPS, SIP)')
                    // ->orWhere('karyawans.site_job', 'CV. SUMBER PANGAN - KEDIRI')
                    // ->orWhere('penempatan_kerja', 'ALL SITES (SP)')
                    // ->take('5')
                    ->select('karyawans.name')
                    ->get()
                    ->toArray();
                $ok4 = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan4_id')
                    ->join('divisis', 'divisis.id', '=', 'karyawans.divisi4_id')
                    ->join('departemens', 'departemens.id', '=', 'karyawans.dept4_id')
                    ->join('bagians', 'bagians.id', '=', 'karyawans.bagian4_id')
                    // ->whereIn('karyawans.site_job', ['ALL SITES (SP, SPS, SIP)', 'CV. SUMBER PANGAN - KEDIRI', 'CV. SUMBER PANGAN - TUBAN', 'ALL SITES (SP)'])
                    // ->where('divisis.nama_divisi', $jabatan["nama_divisi"])
                    // ->where('bagians.nama_bagian', $jabatan["nama_bagian"])
                    ->whereIn('karyawans.site_job', $user_site_job_karyawan)
                    ->where('departemens.nama_departemen', $jabatan_departemen)
                    ->where('divisis.nama_divisi', $jabatan_divisi)
                    ->where('bagians.nama_bagian', $jabatan_bagian)
                    ->where('jabatans.nama_jabatan', $jabatan_karyawan)
                    ->where('karyawans.status_aktif', 'AKTIF')
                    // ->where('karyawans.site_job', 'ALL SITES (SP, SPS, SIP)')
                    // ->orWhere('karyawans.site_job', 'CV. SUMBER PANGAN - KEDIRI')
                    // ->orWhere('penempatan_kerja', 'ALL SITES (SP)')
                    // ->take('5')
                    ->select('karyawans.name')
                    ->get()
                    ->toArray();
                // dd($ok);
                if ($ok == []) {
                    $user_name = NULL;
                } else {
                    // dd(json_encode($ok));
                    $user_name = str_replace('[{', '', json_encode($ok));
                    $user_name = str_replace('{', '<li>', $user_name);
                    $user_name = str_replace('"', '', $user_name);
                    $user_name = str_replace('}', '', $user_name);
                    $user_name = str_replace(']', '', $user_name);
                    $user_name = str_replace('name:', ' ', $user_name);
                    $user_name = str_replace(' ', '&nbsp;', $user_name);
                    $user_name = str_replace(',', '</li>', $user_name);
                }

                if ($ok1 == []) {
                    $user_name1 = NULL;
                } else {
                    $user_name1 = str_replace('[{', '', json_encode($ok1));
                    $user_name1 = str_replace('{', '<li>', $user_name1);
                    $user_name1 = str_replace('"', '', $user_name1);
                    $user_name1 = str_replace('}', '', $user_name1);
                    $user_name1 = str_replace(']', '', $user_name1);
                    $user_name1 = str_replace('name:', ' ', $user_name1);
                    $user_name1 = str_replace(' ', '&nbsp;', $user_name1);
                    $user_name1 = str_replace(',', '</li>', $user_name1);
                }
                if ($ok2 == []) {
                    $user_name2 = NULL;
                } else {
                    $user_name2 = str_replace('[{', '', json_encode($ok2));
                    $user_name2 = str_replace('{', '<li>', $user_name2);
                    $user_name2 = str_replace('"', '', $user_name2);
                    $user_name2 = str_replace('}', '', $user_name2);
                    $user_name2 = str_replace(']', '', $user_name2);
                    $user_name2 = str_replace('name:', ' ', $user_name2);
                    $user_name2 = str_replace(' ', '&nbsp;', $user_name2);
                    $user_name2 = str_replace(',', '</li>', $user_name2);
                }
                if ($ok3 == []) {
                    $user_name3 = NULL;
                } else {
                    $user_name3 = str_replace('[{', '', json_encode($ok3));
                    $user_name3 = str_replace('{', '<li>', $user_name3);
                    $user_name3 = str_replace('"', '', $user_name3);
                    $user_name3 = str_replace('}', '', $user_name3);
                    $user_name3 = str_replace(']', '', $user_name3);
                    $user_name3 = str_replace('name:', ' ', $user_name3);
                    $user_name3 = str_replace(' ', '&nbsp;', $user_name3);
                    $user_name3 = str_replace(',', '</li>', $user_name3);
                }
                if ($ok4 == []) {
                    $user_name4 = NULL;
                } else {
                    $user_name4 = str_replace('[{', '', json_encode($ok4));
                    $user_name4 = str_replace('{', '<li>', $user_name4);
                    $user_name4 = str_replace('"', '', $user_name4);
                    $user_name4 = str_replace('}', '', $user_name4);
                    $user_name4 = str_replace(']', '', $user_name4);
                    $user_name4 = str_replace('name:', ' ', $user_name4);
                    $user_name4 = str_replace(' ', '&nbsp;', $user_name4);
                    $user_name4 = str_replace(',', '</li>', $user_name4);
                }
                // $role = '<a class="btn btn-sm btn-primary"> oke</a>';
                $count_username = (count($ok) + count($ok1) + count($ok2) + count($ok3) + count($ok4)) . '&nbsp;Karyawan';

                $foto = '<img width=30 height=30 style="border-radius: 50%;" align=center margin_bottom=4 margin_top=4 src=https://hrd.sumberpangan.store:4430/public/admin/assets/img/avatars/1.png><br>';
                $jabatan_struktur[] = array('x' => $jabatan_karyawan . ' <br>(' . $jabatan_bagian . ')', 'id' => str_replace("-", "", $jabatan_karyawan_id), 'parent' => str_replace("-", "", $jabatan_karyawan_atasan), 'user' => $user_name  . $user_name1 . $user_name2 . $user_name3 . $user_name4, 'attributes' => array('role' => $count_username, 'photo' => $foto));
                // $jabatan_struktur[] = array('x' => $jabatan['nama_jabatan'] . ' (' . $jabatan['nama_bagian'] . ')', 'id' => str_replace("-", "", $jabatan['id']), 'parent' => str_replace("-", "", $jabatan['atasan_id']), 'attributes' => array('role' => $user_name  . $user_name1 . $user_name2 . $user_name3 . $user_name4, 'photo' => $foto));
            }
            // dd($jabatan_struktur);
        }
        return view('users.profile.lihat_struktur_organisasi', [
            'title' => 'Profile',
            'user' => $jabatan_struktur,
        ]);
    }
}
