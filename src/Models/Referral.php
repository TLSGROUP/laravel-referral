<?php

namespace TLSGROUP\LaravelReferral\Models;

use App\Models\User; // Мы можем использовать модель User напрямую
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    /**
     * Атрибуты, которые могут быть назначены.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'referral_code', 'referrer_id', 'level'
    ];
    /**
     * Получить пользователя, связанного с этим рефералом.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Проверить, есть ли у пользователя свободные места на текущем уровне.
     * Пользователь может иметь максимум 25 рефералов на одном уровне.
     */
    public static function hasAvailableSlot($userId, $level)
    {
        $referralsCount = self::where('referrer_id', $userId)->where('level', $level)->count();
        return $referralsCount < 25;  // Максимум 25 рефералов на уровне
    }
    /**
     * Найти доступный слот для нового реферала (слева направо) на уровне.
     */
    public static function findAvailableSlot($referrerId, $level)
    {
        // Проверяем текущего пригласившего
        if (self::hasAvailableSlot($referrerId, $level)) {
            return $referrerId;
        }

        // Ищем рефералов на предыдущем уровне, чтобы найти доступный слот для нового реферала
        $referrals = self::where('referrer_id', $referrerId)->where('level', $level)->get();

        // Проверяем рефералов на данном уровне
        foreach ($referrals as $referral) {
            if (self::hasAvailableSlot($referral->user_id, $level + 1)) {
                return $referral->user_id; // Возвращаем ID реферала, у которого есть свободный слот
            }
        }

        return null; // Если нет доступных слотов
    }
    /**
     * Получить пригласившего пользователя (реферера).
     *
     * @return BelongsTo
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }
    /**
     * Найти пользователя по реферальному коду.
     *
     * @param string $code
     * @return mixed|null
     */
    public static function userByReferralCode(string $code): mixed
    {
        // Пытаемся найти реферал по коду в таблице referrals
        $referrer = self::where('referral_code', $code)->first();
        if ($referrer) {
            // Если нашли, возвращаем пользователя, которого этот реферал пригласил
            return User::find($referrer->user_id);
        }
        // Если не нашли в таблице referrals, ищем в таблице users
        return User::where('referral_code', $code)->first();
    }
}

