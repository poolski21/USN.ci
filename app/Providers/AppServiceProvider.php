<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use App\Models\SocialMessage;
use App\Models\SocialNotification;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr', 'French_France.1252');

        View::composer('partials.navbar', function ($view) {
            $unreadMessages = 0;
            $unreadNotifications = 0;

            if (Auth::check()) {
                $userId = Auth::id();
                $unreadMessages = SocialMessage::where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count();
                $unreadNotifications = SocialNotification::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with(compact('unreadMessages', 'unreadNotifications'));
        });
    }
}
