<?php

namespace App\Services;

use App\Models\Cuti;
use App\Models\CutiDetail;
use App\Models\IzinSakit;
use App\Models\IzinSakitDetail;
use App\Models\Karyawan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class KaryawanPenggantiService
{
    /**
     * Mencari profil karyawan aktif yang bertugas pada tanggal tertentu.
     * Jika karyawan asli sedang cuti / sakit, fungsi ini akan menelusuri
     * karyawan_pengganti (termasuk rantai pengganti bertingkat / chained substitution).
     *
     * @param Karyawan|string|null $subject Karyawan model, Karyawan UUID/ID, atau User ID/NIK
     * @param Carbon|string|null $targetDate Tanggal pengecekan (default: hari ini)
     * @return array{
     *     karyawan: ?Karyawan,
     *     karyawan_asli: ?Karyawan,
     *     is_pengganti: bool,
     *     alasan: ?string
     * }
     */
    public function cariKaryawanPenggantiAktif($subject, $targetDate = null): array
    {
        $karyawanAsli = $this->temukanKaryawan($subject);
        if (!$karyawanAsli) {
            return [
                'karyawan'      => null,
                'karyawan_asli' => null,
                'is_pengganti'  => false,
                'alasan'        => null,
            ];
        }
        $tanggalObj = $targetDate instanceof Carbon
            ? $targetDate
            : ($targetDate ? Carbon::parse($targetDate) : now());
        $tanggalFormatted = $tanggalObj->format('Y-m-d');
        $visited = []; // Mencegah looping berulang jika A -> B -> A
        $currentKaryawan = $karyawanAsli;
        $isPengganti = false;
        $alasanTerakhir = null;
        while ($currentKaryawan && !in_array((string) $currentKaryawan->id, $visited, true)) {
            $visited[] = (string) $currentKaryawan->id;
            $infoPengganti = $this->ambilInfoPenggantiUntukTanggal((string) $currentKaryawan->id, $tanggalFormatted);
            if (!$infoPengganti) {
                // Karyawan ini hadir / tidak ada cuti atau sakit aktif pada tanggal tersebut
                break;
            }

            $penggantiKaryawan = $this->temukanKaryawan($infoPengganti['karyawan_pengganti_id']);
            if (!$penggantiKaryawan) {
                // ID karyawan pengganti tidak ditemukan di tabel karyawan, tetap gunakan karyawan ini
                break;
            }

            $isPengganti = true;
            $alasanTerakhir = $infoPengganti['alasan'] ?? 'Cuti / Ijin';
            $currentKaryawan = $penggantiKaryawan;
        }
        return [
            'karyawan'      => $currentKaryawan,
            'karyawan_asli' => $karyawanAsli,
            'is_pengganti'  => $isPengganti,
            'alasan'        => $alasanTerakhir,
        ];
    }

    /**
     * Memeriksa apakah karyawan sedang memiliki pengajuan cuti atau sakit yang disetujui pada tanggal tersebut.
     * Mengembalikan array informasi pengganti atau null jika tidak ada.
     *
     * @param string $karyawanId
     * @param string $tanggalFormatted Format 'Y-m-d'
     * @return array{karyawan_pengganti_id: string, alasan: string}|null
     */
    public function ambilInfoPenggantiUntukTanggal(string $karyawanId, string $tanggalFormatted): ?array
    {
        // 1. Cek Cuti (status_cuti: 1 = approved atasan1, 2 = approved penuh)
        $cuti = Cuti::where('karyawan_id', $karyawanId)
            ->whereIn('status_cuti', [1, 2])
            ->where(function ($query) use ($tanggalFormatted) {
                $query->where(function ($q) use ($tanggalFormatted) {
                    $q->where('tanggal_mulai', '<=', $tanggalFormatted)
                      ->where('tanggal_selesai', '>=', $tanggalFormatted);
                })
                ->orWhereHas('CutiDetail', function ($detail) use ($tanggalFormatted) {
                    $detail->whereDate('tgl_cuti', $tanggalFormatted);
                });
            })
            ->whereNotNull('karyawan_pengganti_id')
            ->where('karyawan_pengganti_id', '!=', '')
            ->first();

        if ($cuti && !empty($cuti->karyawan_pengganti_id)) {
            $namaCuti = $cuti->nama_cuti ?: 'Cuti';
            return [
                'karyawan_pengganti_id' => (string) $cuti->karyawan_pengganti_id,
                'alasan'                => "Sedang {$namaCuti} ({$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai})",
            ];
        }

        // 2. Cek Izin Sakit (status_sakit: 1 = proses/approved 1, 2 = approved atasan2, 3 = approved hrd)
        $sakit = IzinSakit::where('karyawan_id', $karyawanId)
            ->whereIn('status_sakit', [1, 2, 3])
            ->whereHas('IzinSakitDetail', function ($detail) use ($tanggalFormatted) {
                $detail->whereDate('tgl_sakit', $tanggalFormatted);
            })
            ->whereNotNull('karyawan_pengganti_id')
            ->where('karyawan_pengganti_id', '!=', '')
            ->first();
        if ($sakit && !empty($sakit->karyawan_pengganti_id)) {
            return [
                'karyawan_pengganti_id' => (string) $sakit->karyawan_pengganti_id,
                'alasan'                => "Sedang Izin Sakit ({$sakit->tanggal_sakit})",
            ];
        }
        return null;
    }

    /**
     * Dapatkan daftar ID karyawan yang saat ini (pada tanggal tertentu) sedang didelegasikan
     * kepada $karyawanPenggantiId karena yang bersangkutan sedang cuti / sakit.
     * Digunakan untuk hak akses web (reverse lookup).
     *
     * @param string $karyawanPenggantiId ID karyawan yang ditunjuk sebagai pengganti
     * @param Carbon|string|null $targetDate
     * @return array<string> Daftar ID karyawan asli yang digantikan
     */
    public function ambilDaftarIdKaryawanYangDigantikan(string $karyawanPenggantiId, $targetDate = null): array
    {
        if (!$karyawanPenggantiId) {
            return [];
        }
        $tanggalObj = $targetDate instanceof Carbon
            ? $targetDate
            : ($targetDate ? Carbon::parse($targetDate) : now());
        $tanggalFormatted = $tanggalObj->format('Y-m-d');
        // 1. Karyawan yang cuti dan menunjuk karyawan ini sebagai pengganti
        $karyawanCuti = Cuti::where('karyawan_pengganti_id', $karyawanPenggantiId)
            ->whereIn('status_cuti', [1, 2])
            ->where(function ($query) use ($tanggalFormatted) {
                $query->where(function ($q) use ($tanggalFormatted) {
                    $q->where('tanggal_mulai', '<=', $tanggalFormatted)
                      ->where('tanggal_selesai', '>=', $tanggalFormatted);
                })
                ->orWhereHas('CutiDetail', function ($detail) use ($tanggalFormatted) {
                    $detail->whereDate('tgl_cuti', $tanggalFormatted);
                });
            })
            ->pluck('karyawan_id')
            ->map(fn($id) => (string) $id)
            ->toArray();
        // 2. Karyawan yang sakit dan menunjuk karyawan ini sebagai pengganti
        $karyawanSakit = IzinSakit::where('karyawan_pengganti_id', $karyawanPenggantiId)
            ->whereIn('status_sakit', [1, 2, 3])
            ->whereHas('IzinSakitDetail', function ($detail) use ($tanggalFormatted) {
                $detail->whereDate('tgl_sakit', $tanggalFormatted);
            })
            ->pluck('karyawan_id')
            ->map(fn($id) => (string) $id)
            ->toArray();
        $daftarId = array_values(array_unique(array_filter(array_merge($karyawanCuti, $karyawanSakit))));
        return $daftarId;
    }

    /**
     * Helper untuk menemukan Model Karyawan berdasarkan ID, UUID, NIK, atau User ID.
     */
    public function temukanKaryawan($subject): ?Karyawan
    {
        if ($subject instanceof Karyawan) {
            return $subject;
        }
        if (!$subject) {
            return null;
        }
        $subjectStr = (string) $subject;
        // Cari berdasarkan UUID / ID karyawan
        if (Uuid::isValid($subjectStr)) {
            $karyawan = Karyawan::find($subjectStr);
            if ($karyawan) {
                return $karyawan;
            }
        }
        // Cari berdasarkan nomor identitas karyawan (NIK)
        $karyawanByNik = Karyawan::where('nomor_identitas_karyawan', $subjectStr)->first();
        if ($karyawanByNik) {
            return $karyawanByNik;
        }
        // Cari berdasarkan User
        $user = User::find($subjectStr);
        if ($user && $user->karyawan_id) {
            return $this->temukanKaryawan((string) $user->karyawan_id);
        }
        return null;
    }
}
