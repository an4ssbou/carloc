<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GpsTrackingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiclePhotoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('agency/vehicles/{vehicle}/gps')->name('agency.vehicles.gps')->group(function () {
    Route::get('/', [GpsTrackingController::class, 'show']);
    Route::get('/position', [GpsTrackingController::class, 'position'])->name('.position');
    Route::post('/token', [GpsTrackingController::class, 'token'])->name('.token');
    Route::delete('/token', [GpsTrackingController::class, 'revoke'])->name('.token.revoke');
    Route::post('/simulate', [GpsTrackingController::class, 'simulate'])->name('.simulate');
});

Route::middleware(['auth'])->group(function () {
    // ... tes autres routes (profile, reservations, etc.)

    // Page des notifications (Gère l'affichage ET le marquage automatique comme lu)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Route pour marquer une notification spécifique (si besoin d'un bouton précis)
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

// =======================
// RESERVATION + PAYMENT
// =======================
Route::middleware(['auth', 'verified'])->group(function () {

    // 📄 Page réservation (formulaire)
    Route::get('/reservation/{vehicle}', [ReservationController::class, 'create'])
        ->name('reservation.create');

    // 💾 Enregistrer réservation
    Route::post('/reservation', [ReservationController::class, 'store'])
        ->name('reservation.store');

    // 💳 Paiement Stripe
    Route::post('/payment', [PaymentController::class, 'pay'])
        ->name('payment.pay');

});
Route::post('/reservation/{reservation}/simulate-payment', [PaymentController::class, 'simulate'])
    ->middleware(['auth', 'verified'])->name('reservation.simulate-payment');
Route::get('/reservation/{reservation}/contract', [ReservationController::class, 'contract'])
    ->middleware('auth')->name('reservation.contract');

//  Liste des réservations utilisateur
Route::get('/my-reservations', [ReservationController::class, 'index'])
    ->middleware('auth')
    ->name('reservation.index');

// 👁 Voir une réservation
Route::get('/reservation/{reservation}/show', [ReservationController::class, 'show'])
    ->middleware('auth')
    ->name('reservation.show');

//  Annuler réservation
Route::post('/reservation/{reservation}/cancel', [ReservationController::class, 'cancel'])
    ->middleware('auth')
    ->name('reservation.cancel');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

Route::get('/agencies', [AgencyController::class, 'index'])->name('agency.index');
Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])
    ->whereUuid('vehicle')
    ->name('vehicles.show');
Route::get('/agencies/{slug}', [VehicleController::class, 'index'])->name('agency.vehicles');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/agency/{slug}/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('agency.dashboard');

Route::get('/agency/{slug}/reservations', [DashboardController::class, 'reservations'])
    ->middleware(['auth', 'verified'])
    ->name('agency.reservations.index');

Route::post('/agency/{slug}/reservations/{reservation}/confirm', [DashboardController::class, 'confirmReservation'])
    ->middleware(['auth', 'verified'])
    ->name('agency.reservations.confirm');

Route::post('/agency/{slug}/reservations/{reservation}/cancel', [DashboardController::class, 'cancelReservation'])
    ->middleware(['auth', 'verified'])
    ->name('agency.reservations.cancel');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/license_photo', [ProfileController::class, 'showLicense'])->name('profile.license_photo');
    Route::get('/profile/cin_photo', [ProfileController::class, 'showCin'])->name('profile.cin_photo');

    Route::get('/agency/my/vehicles', [VehicleController::class, 'agencyVehicles'])->name('agency.vehicles.manage');
    Route::resource('vehicles', VehicleController::class)->except(['index', 'show']);

    Route::post('/vehicles/{vehicle}/photos', [VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
    Route::patch('/vehicles/{vehicle}/photos/{photo}/primary', [VehiclePhotoController::class, 'setPrimary'])->name('vehicles.photos.primary');
    Route::delete('/vehicles/{vehicle}/photos/{photo}', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');

    Route::get('/agency/edit/{slug}', [AgencyController::class, 'edit'])->name('agency.edit');
    Route::get('/agency/create', [AgencyController::class, 'create'])->name('agency.create');
    Route::post('/agency', [AgencyController::class, 'store'])->name('agency.store');
    Route::patch('/agency/{slug}', [AgencyController::class, 'update'])->name('agency.update');
    Route::delete('/agency/{slug}', [AgencyController::class, 'destroy'])->name('agency.destroy');

    Route::prefix('admin')->name('admin.')->middleware('super_admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/agencies', [AdminDashboardController::class, 'agencies'])->name('agencies');
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/vehicles', [AdminDashboardController::class, 'vehicles'])->name('vehicles');
        Route::get('/reservations', [AdminDashboardController::class, 'reservations'])->name('reservations');

        Route::get('/agencies/{slug}/edit', [AdminDashboardController::class, 'editAgency'])->name('agencies.edit');
        Route::patch('/agencies/{slug}', [AdminDashboardController::class, 'updateAgency'])->name('agencies.update');
        Route::delete('/agencies/{slug}', [AdminDashboardController::class, 'destroyAgency'])->name('agencies.destroy');

        Route::patch('/agencies/{agency}/approve', [AdminDashboardController::class, 'approveAgency'])->name('agencies.approve');
        Route::patch('/agencies/{agency}/reject', [AdminDashboardController::class, 'rejectAgency'])->name('agencies.reject');
        Route::patch('/agencies/{agency}/suspend', [AdminDashboardController::class, 'suspendAgency'])->name('agencies.suspend');
        Route::post('/reservations/{reservation}/confirm', [AdminDashboardController::class, 'confirmReservation'])->name('reservations.confirm');
        Route::post('/reservations/{reservation}/cancel', [AdminDashboardController::class, 'cancelReservation'])->name('reservations.cancel');
    });
});

require __DIR__.'/auth.php';
