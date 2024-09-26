<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Пользователь, получивший реферальный код
            $table->string('referral_code')->unique(); // Уникальный реферальный код
            $table->unsignedBigInteger('referrer_id')->nullable(); // Пригласивший пользователь (может быть null)
            $table->integer('level')->default(1); // Уровень (линия) пользователя
            $table->timestamps();

            // Внешние ключи
            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Индексы для ускорения поиска
            $table->index('referral_code');
            $table->index('user_id');
            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}
