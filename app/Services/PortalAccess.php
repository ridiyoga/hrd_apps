<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PortalAccess
{
    // Agile bertanya ke portal melalui server, bukan mempercayai kartu/parameter browser.
    public function allows(string $subject): bool
    {
        $response = Http::withToken(config('sso.portal_access_secret'))->timeout(5)->get(config('sso.portal_access_url'), [
            'issuer' => config('sso.issuer'), 'subject' => $subject,
        ]);
        if (! $response->successful()) {
            throw new RuntimeException('Pemeriksaan akses portal gagal.');
        }

        return $response->json('allowed') === true;
    }
}
