<?php

return [

    'merchant_code' => env('DUITKU_MERCHANT_CODE'),
    'api_key'       => env('DUITKU_API_KEY'),

    'callback_url'  => env('DUITKU_CALLBACK_URL'),
    'return_url'    => env('DUITKU_RETURN_URL'),

    'env' => env('DUITKU_ENV', 'sandbox'),
];
