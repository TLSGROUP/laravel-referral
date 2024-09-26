<?php

namespace TLSGROUP\LaravelReferral\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class ReferralController extends Controller
{
    /**
     * Assign a referral code to the user by setting a cookie.
     *
     * @param string $referralCode
     * @return RedirectResponse
     */
    public function assignReferrer(string $referralCode): RedirectResponse
    {
        $refCookieName = config('referral.cookie_name');
        $refCookieExpiry = config('referral.cookie_expiry');
        if (Cookie::has($refCookieName)) {
            return redirect()->route(config('referral.redirect_route'));
        } else {
            $ck = Cookie::make($refCookieName, $referralCode, $refCookieExpiry);
            return redirect()->route(config('referral.redirect_route'))->withCookie($ck);
        }
    }
    /**
     * Generate referral codes for existing users.
     *
     * @return JsonResponse
     */
    public function createReferralCodeForExistingUsers(): JsonResponse
    {
        $userModel = resolve(config('auth.providers.users.model'));
        $users = $userModel::cursor();
        foreach ($users as $user) {
            if (!$user->hasReferralAccount()) {
                $user->generateReferralCode(); // Используем общий метод для генерации
            }
        }
        return response()->json(['message' => 'Referral codes generated for existing users.']);
    }
}
