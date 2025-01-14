<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Client\WebhookClient::class,
            \App\Infrastructure\Clients\WebhookClient::class
        );

        $this->app->bind(
            \App\Domain\Repository\Repository::class,
            \App\Infrastructure\Db\Repositories\Repository::class
        );

        $this->app->bind(
            \App\Domain\Repository\WebhookRepository::class,
            \App\Infrastructure\Db\Repositories\WebhookRepository::class
        );

        $this->app->bind(
            \App\Domain\Repository\QueueItemRepository::class,
            \App\Infrastructure\Db\Repositories\QueueItemRepository::class
        );

        $this->app->bind(
            \App\Domain\Repository\FailedAttemptRepository::class,
            \App\Infrastructure\Db\Repositories\FailedAttemptRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
