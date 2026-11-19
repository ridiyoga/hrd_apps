> Konfigurasi LAN aktif. Akun portal admin01@gmail.com dipetakan ke username HRD superadmin3. Panduan lengkap: proyek portal_superapp/docs/SSO-LAN.md.

# SSO HRD

HRD menggunakan OIDC Keycloak dengan realm yang sama seperti Agile. Buat client Keycloak terpisah untuk HRD.

## Konfigurasi Keycloak

- Client ID: `hrd`
- Client protocol: `openid-connect`
- Flow: standard authorization code dengan PKCE S256
- Valid redirect URI: nilai `OIDC_REDIRECT_URI`
- Valid post logout redirect URI: nilai `OIDC_LOGOUT_URI`
- Client authentication: aktif untuk client confidential

Tambahkan nilai berikut ke `.env` HRD:

```dotenv
SSO_ENABLED=true
OIDC_ISSUER=http://192.168.3.240:8080/realms/internal
OIDC_CLIENT_ID=hrd
OIDC_CLIENT_SECRET=isi-secret-client-hrd
OIDC_REDIRECT_URI=http://192.168.3.240:8002/auth/callback
OIDC_LOGOUT_URI=http://192.168.3.240:8002/login
SESSION_COOKIE=hrd_session
```

Jalankan migration dan bersihkan cache konfigurasi:

```powershell
php artisan migrate
php artisan config:clear
```

## Hubungkan Akun

SSO tidak membuat akun HRD otomatis dan tidak menyalin password. Hubungkan `subject` Keycloak ke akun HRD yang sudah ada:

```powershell
php artisan sso:link-user username-atau-email SUBJECT_KEYCLOAK
```

`SUBJECT_KEYCLOAK` adalah nilai `sub` dari user Keycloak. Mapping disimpan berdasarkan pasangan `issuer + subject`.

## Endpoint

- `GET /auth/sso` memulai login Keycloak.
- `GET /auth/callback` menerima authorization code dan membuat sesi HRD.
- `POST /auth/logout` mengakhiri sesi HRD dan mengarahkan logout ke Keycloak.

Sesi SSO diperiksa ulang melalui endpoint introspection Keycloak. Login lokal tetap tersedia sebagai fallback selama migrasi akun.
