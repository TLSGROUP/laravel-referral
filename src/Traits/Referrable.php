<?php

namespace TLSGROUP\LaravelReferral\Traits;

use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use TLSGROUP\LaravelReferral\Models\Referral;

trait Referrable
{
    /**
     * Get the referrals associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referral account of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralAccount()
    {
        return $this->belongsTo(Referral::class, 'id', 'user_id');
    }

    /**
     * Check if the user has a referral account.
     *
     * @return bool
     */
    public function hasReferralAccount(): bool
    {
        return !is_null($this->referralAccount);
    }

    /**
     * Get the referral link for the user.
     *
     * @return string
     */
    public function getReferralLink(): string
    {
        if ($this->hasReferralAccount()) {
            return url('/') . "/" . config('referral.route_prefix') . "/" . $this->getReferralCode();
        }
        return "";
    }

    /**
     * Get the referral code of the user's referral account.
     *
     * @return string|null
     */
    public function getReferralCode(): ?string
    {
        if ($this->hasReferralAccount()) {
            return $this->referralAccount->referral_code;
        }
        return null;
    }

    /**
     * Создать реферальный аккаунт для пользователя, распределяя его по уровням.
     *
     * @param int|null $referrerID
     * @return void
     * @throws Exception
     */
    public function createReferralAccount(int $referrerID = null): void
    {
        // Начальный уровень — 1
        $level = 1;

        // Ищем доступный слот для реферала
        if ($referrerID) {
            $availableSlot = Referral::findAvailableSlot($referrerID, $level);

            // Если есть доступный слот, назначаем нового реферала
            if ($availableSlot) {
                $referrerID = $availableSlot;
            } else {
                // Если слотов нет, увеличиваем уровень и ищем слот на следующем уровне
                $level++;
                $referrerID = Referral::findAvailableSlot($referrerID, $level);
            }
        }

        // Генерация реферального кода для текущего пользователя
        $referralCode = $this->generateReferralCode();

        // Создаем запись о реферале
        Referral::create([
            'user_id' => $this->id,
            'referrer_id' => $referrerID,
            'referral_code' => $referralCode,
            'level' => $level,  // Устанавливаем уровень для реферала
        ]);
    }

    /**
     * Генерация реферального кода с использованием ref_id, имени и фамилии.
     *
     * @return string
     * @throws Exception
     */
    public function generateReferralCode(): string
    {
        $user = $this;

        // Если ref_id не установлен, генерируем следующий порядковый номер
        if (!$user->ref_id) {
            $maxRefId = User::max('ref_id'); // Находим максимальный существующий ref_id
            $user->ref_id = $maxRefId ? $maxRefId + 1 : 1; // Устанавливаем следующий порядковый номер
        }

        // Генерация инициалов пользователя (первая буква имени и фамилии)
        $initials = strtoupper(substr($user->name, 0, 1) . substr($user->last_name, 0, 1));

        // Префикс для реферального кода
        $prefix = 'DTC';

        // Генерация реферального кода с использованием префикса, инициалов и ref_id
        $referralCode = $prefix . $initials . str_pad($user->ref_id, 2, '0', STR_PAD_LEFT);

        // Проверка уникальности реферального кода
        if (User::where('referral_code', $referralCode)->exists()) {
            throw new Exception("Generated referral code already exists: $referralCode");
        }

        // Сохраняем изменения, если ref_id был изменён
        $user->save();

        return $referralCode;
    }
}

