<?php

namespace App\Providers;

use App\Models\Agency;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use App\Observers\AgencyObserver;
use App\Observers\ReservationObserver;
use App\Observers\VehicleObserver;
use App\Observers\VehiclePhotoObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        if (config('demo.enabled')) {
            \App\Support\PublicDemoPolicy::validate(['enabled'=>true,'environment'=>config('app.env'),'debug'=>config('app.debug'),'connection'=>config('database.default'),'database'=>config('database.connections.sqlite.database'),'url'=>config('app.url'),'mailer'=>'array','allow_local'=>config('demo.allow_local')]);
            config(['mail.default'=>'array','queue.default'=>'sync']);
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            if (!config('demo.allow_local')) \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        RateLimiter::for('auth-sensitive', fn (Request $request) => Limit::perMinute(5)->by($request->path().'|'.$request->ip())
        );
        Reservation::observe(ReservationObserver::class);
        Vehicle::observe(VehicleObserver::class);
        VehiclePhoto::observe(VehiclePhotoObserver::class);
        Agency::observe(AgencyObserver::class);
    }
}
