<?php

return [
    // HTTP diperbolehkan hanya pada host LAN eksplisit di lingkungan local/testing.
    'http_allowed_hosts' => array_filter(['localhost', '127.0.0.1', env('SSO_HTTP_HOST')]),
    'portal_access_url' => env('PORTAL_ACCESS_URL'),
    'portal_access_secret' => env('PORTAL_ACCESS_SECRET'),
    'enabled' => env('SSO_ENABLED', false),
    'issuer' => env('OIDC_ISSUER'),
    'client_id' => env('OIDC_CLIENT_ID', 'hrd'),
    'client_secret' => env('OIDC_CLIENT_SECRET'),
    'redirect_uri' => env('OIDC_REDIRECT_URI'),
    'logout_uri' => env('OIDC_LOGOUT_URI'),
];