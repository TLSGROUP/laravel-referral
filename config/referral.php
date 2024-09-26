<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Referral System Configuration
    |--------------------------------------------------------------------------
    | This file contains the configuration options for the referral system.
    */
    // The name of the referral cookie
    'cookie_name' => 'dtc_ref',

    // Expiry time for the referral cookie in minutes
    'cookie_expiry' => 525600, // 1 year

    // The prefix used for referral links
    'route_prefix' => 'user/register', // Оставляем или меняем под ваш маршрут

    // The prefix used for referral code
    'ref_code_prefix' => 'DTC', // Изменено на DTC, чтобы соответствовать вашей системе

    // The route where users will be redirected after clicking on a referral link
    'redirect_route' => 'register', // Маршрут, куда будет перенаправлен пользователь

    // The model class for the user
    'user_model' => 'App\Models\User',
];

