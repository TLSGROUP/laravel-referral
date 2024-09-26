<?php

namespace TLSGROUP\LaravelReferral\Providers;

use Illuminate\Support\ServiceProvider;

class ReferralServiceProvider extends ServiceProvider
{
    /**
     * Регистрация сервисов пакета.
     *
     * @return void
     */
    public function register(): void
    {
        // Мерджим конфиг пакета с конфигом приложения
        $this->mergeConfigFrom(__DIR__.'/../../config/referral.php', 'referral');
    }
    /**
     * Загрузка сервисов пакета.
     *
     * @return void
     */
    public function boot(): void
    {
        // Загрузка миграций пакета
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        if ($this->app->runningInConsole()) {
            // Публикация конфигурационного файла пакета
            $this->publishes([
                __DIR__.'/../../config/referral.php' => config_path('referral.php'),
            ], 'laravel-referral-config');
            // Публикация файлов миграций пакета
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'laravel-referral-migrations');
        }
        // Загрузка маршрутов пакета
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
