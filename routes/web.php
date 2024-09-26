<?php

use Illuminate\Support\Facades\Route;
use TLSGROUP\LaravelReferral\Controllers\ReferralController;

Route::middleware('web')->group(function () {
    // Маршрут для отслеживания реферального кода и назначения пригласившего
    Route::get(config('referral.route_prefix') . '/{referralCode}', [ReferralController::class, 'assignReferrer'])
        ->name('referralLink');

    // Маршрут для генерации реферальных кодов для существующих пользователей
    Route::get('generate-ref-accounts', [ReferralController::class, 'createReferralCodeForExistingUsers'])
        ->name('generateReferralCodes');
});

