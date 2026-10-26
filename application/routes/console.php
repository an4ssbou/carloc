<?php

use App\Observers\VehicleObserver;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('vehicles:sync-status', function () {
    VehicleObserver::syncStatuses();

    $this->info('Vehicle statuses synchronized successfully.');
})->purpose('Synchronize vehicle availability based on reservation periods');

Schedule::command('vehicles:sync-status')->everyMinute();

Schedule::command('reservations:send-reminders')->dailyAt('08:00');