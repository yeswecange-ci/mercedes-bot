<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Conversation;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share active conversations count with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $activeCount = Conversation::where('status', 'active')->count();
                $view->with('activeCount', $activeCount);
            }
        });
    }
}
