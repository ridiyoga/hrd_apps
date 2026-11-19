<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME', 'smtp'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],
        'hrd' => [
            'transport' => 'smtp',
            'scheme' => env('HRD_MAIL_SCHEME', env('MAIL_SCHEME')),
            'url' => env('HRD_MAIL_URL', env('MAIL_URL')),
            'host' => env('HRD_MAIL_HOST', env('MAIL_HOST', '127.0.0.1')),
            'port' => env('HRD_MAIL_PORT', env('MAIL_PORT', 2525)),
            'encryption' => env('HRD_MAIL_ENCRYPTION', env('MAIL_ENCRYPTION')),
            'username' => env('HRD_MAIL_USERNAME', env('MAIL_USERNAME')),
            'password' => env('HRD_MAIL_PASSWORD', env('MAIL_PASSWORD')),
            'timeout' => null,
            'local_domain' => env('HRD_MAIL_EHLO_DOMAIN', env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST))),

            'from' => [
                'address' => env('HRD_MAIL_FROM_ADDRESS', 'no_reply@beraskediri.com'),
                'name' => env('HRD_MAIL_FROM_NAME', 'HRD Beras Kediri'),
            ],
        ],

        // ── Mailer khusus untuk notifikasi Ticket Support ──────────────────────
        // Mailer ini menggunakan akun email tersendiri, terpisah dari HRD no-reply.
        // Isi TICKET_MAIL_USERNAME dan TICKET_MAIL_PASSWORD di file .env
        'ticket' => [
            'transport'   => 'smtp',
            'scheme'      => env('TICKET_MAIL_SCHEME', 'smtp'),
            'host'        => env('TICKET_MAIL_HOST', '127.0.0.1'),
            'port'        => env('TICKET_MAIL_PORT', 465),
            'encryption'  => env('TICKET_MAIL_ENCRYPTION', 'ssl'),
            'username'    => env('TICKET_MAIL_USERNAME'),
            'password'    => env('TICKET_MAIL_PASSWORD'),
            'timeout'     => null,
            'local_domain' => env('TICKET_MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),

            'from' => [
                'address' => env('TICKET_MAIL_FROM_ADDRESS', 'ticket@beraskediri.com'),
                'name'    => env('TICKET_MAIL_FROM_NAME', 'Ticket Support Beras Kediri'),
            ],
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];
