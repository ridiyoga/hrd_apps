<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LinkSsoUser extends Command
{
    protected $signature = 'sso:link-user {user : Email atau username akun HRD} {subject : User ID dari Keycloak}';

    protected $description = 'Hubungkan akun HRD yang sudah ada ke subject Keycloak';

    public function handle(): int
    {
        $issuer = rtrim((string) config('sso.issuer'), '/');
        $subject = (string) $this->argument('subject');
        $login = (string) $this->argument('user');
        $user = User::where('username', $login)
            ->orWhereRaw('LOWER(email) = ?', [strtolower($login)])
            ->first();

        if (! $user || ! $issuer || $subject === '' || strlen($subject) > 255) {
            $this->error('Akun, issuer, atau subject tidak valid.');

            return self::FAILURE;
        }

        $existing = DB::table('external_identities')
            ->where(['issuer' => $issuer, 'subject' => $subject])
            ->first();
        if ($existing && (string) $existing->user_id !== (string) $user->id) {
            $this->error('Identitas SSO sudah dimiliki akun lain.');

            return self::FAILURE;
        }

        DB::table('external_identities')->updateOrInsert(
            ['issuer' => $issuer, 'subject' => $subject],
            ['user_id' => $user->id, 'updated_at' => now(), 'created_at' => $existing?->created_at ?? now()]
        );
        $this->info('Identitas SSO berhasil dihubungkan.');

        return self::SUCCESS;
    }
}