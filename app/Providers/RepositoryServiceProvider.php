<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(BaseRepository::class, TaskRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }
}
