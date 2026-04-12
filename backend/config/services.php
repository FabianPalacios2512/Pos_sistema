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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'epayco' => [
        'public_key' => env('EPAYCO_PUBLIC_KEY'),
        'private_key' => env('EPAYCO_PRIVATE_KEY'),
        'p_cust_id_cliente' => env('EPAYCO_P_CUST_ID_CLIENTE'),
        'p_key' => env('EPAYCO_P_KEY'),
        'test_mode' => filter_var(env('EPAYCO_TEST_MODE', 'false'), FILTER_VALIDATE_BOOLEAN),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ],

    'wompi' => [
        'public_key' => env('WOMPI_PUBLIC_KEY'),
        'private_key' => env('WOMPI_PRIVATE_KEY'),
        'events_secret' => env('WOMPI_EVENTS_SECRET'),
        'integrity_secret' => env('WOMPI_INTEGRITY_SECRET'),
        'api_url' => env('WOMPI_API_URL'),
        'currency' => env('WOMPI_CURRENCY', 'COP'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Services Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'groq' => [
        'api_key_1' => env('GROQ_API_KEY_1'),
        'api_key_2' => env('GROQ_API_KEY_2'),
        'api_key_3' => env('GROQ_API_KEY_3'),
        'api_key_4' => env('GROQ_API_KEY_4'),
        'api_key_5' => env('GROQ_API_KEY_5'),
        'api_key_6' => env('GROQ_API_KEY_6'),
        'api_key_7' => env('GROQ_API_KEY_7'),
        'api_key_8' => env('GROQ_API_KEY_8'),
        'api_key_9' => env('GROQ_API_KEY_9'),
        'api_key_10' => env('GROQ_API_KEY_10'),
        'api_key_11' => env('GROQ_API_KEY_11'),
        'api_key_12' => env('GROQ_API_KEY_12'),
        'api_key_13' => env('GROQ_API_KEY_13'),
        'api_key_14' => env('GROQ_API_KEY_14'),
        'api_key_15' => env('GROQ_API_KEY_15'),
        'api_key_16' => env('GROQ_API_KEY_16'),
        'api_key_17' => env('GROQ_API_KEY_17'),
        'api_key_18' => env('GROQ_API_KEY_18'),
        'api_key_19' => env('GROQ_API_KEY_19'),
        'api_key_20' => env('GROQ_API_KEY_20'),
    ],

];
