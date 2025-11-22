<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'id' => Uuid::uuid4(),
            'username' => 'superadmin',
            'user_aktif' => '1',
            // Wajib: Hash::make() memastikan password di-hash dengan Bcrypt
            'password' => Hash::make('admin123'),
            'password_show' => 'admin123',
            'is_admin' => 'admin',
            // ... kolom lainnya ...
        ]);
    }
}
