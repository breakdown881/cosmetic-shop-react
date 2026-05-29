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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/auth/facebook/callback'),
    ],

    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'payment_url' => env('VNPAY_PAYMENT_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL', env('APP_URL') . '/payments/vnpay/return'),
    ],

    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'redirect_url' => env('MOMO_REDIRECT_URL', env('APP_URL') . '/payments/momo/return'),
        'ipn_url' => env('MOMO_IPN_URL', env('APP_URL') . '/payments/momo/ipn'),
        'request_type' => env('MOMO_REQUEST_TYPE', 'payWithMethod'),
    ],

];
