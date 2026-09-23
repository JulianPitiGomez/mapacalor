<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'actas' => [
        'fotos_path' => env('ACTAS_FOTOS_PATH'),
        'fotos_url' => env('ACTAS_FOTOS_URL'),

        // Fecha desde la que el sistema de actas marca las actas simples con
        // operativo_id = -1. Antes de esta fecha la marca no existía, así que la
        // ausencia de -1 no significa nada y el acta se cuenta como manual.
        // El deploy del sistema de actas fue el 22/09/2026 por la tarde, así que el
        // corte es el 23: las actas del 22 anteriores al deploy no llevan la marca.
        'nomenclatura_desde' => env('ACTAS_NOMENCLATURA_DESDE', '2026-09-23'),
    ],

];
