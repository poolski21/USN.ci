<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Auth\Access\Gate;
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
    public function boot(Gate $gate): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        if ($this->app->environment('production') && config('services.flutterwave.env') === 'test') {
            Log::warning('Configuration Flutterwave incohérente : APP_ENV=production mais FLUTTERWAVE_ENV=test. Vérifie les clés live avant le déploiement.');
        }

        $gate->define('access-admin', function ($user) {
            return $user && is_object($user) && ($user->role ?? null) === 'admin';
        });
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr', 'French_France.1252');

        View::composer('partials.navbar', function ($view) {
            $unreadMessages = 0;
            $unreadNotifications = 0;

            if (Auth::check()) {
                $userId = Auth::id();
                
                // Cache unread counts for 5 minutes
                $cacheKey = "user.{$userId}.notifications";
                $cached = Cache::get($cacheKey);
                
                if ($cached === null) {
                    $unreadMessages = SocialMessage::where('receiver_id', $userId)
                        ->whereNull('read_at')
                        ->count();
                    $unreadNotifications = SocialNotification::where('user_id', $userId)
                        ->whereNull('read_at')
                        ->count();
                    
                    Cache::put($cacheKey, compact('unreadMessages', 'unreadNotifications'), now()->addMinutes(5));
                } else {
                    $unreadMessages = $cached['unreadMessages'];
                    $unreadNotifications = $cached['unreadNotifications'];
                }
            }

            $view->with(compact('unreadMessages', 'unreadNotifications'));
        });
    }
}
