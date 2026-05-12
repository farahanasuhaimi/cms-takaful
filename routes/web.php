<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TouchpointController;
use App\Http\Controllers\ReachAngleController;
use App\Http\Controllers\PlanProductController;
use App\Http\Controllers\SettingController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Policyholders
    Route::resource('clients', ClientController::class);

    // Policies (nested under clients)
    Route::post('clients/{client}/policies', [ClientController::class, 'storePolicy'])->name('clients.policies.store');
    Route::put('clients/{client}/policies/{policy}', [ClientController::class, 'updatePolicy'])->name('clients.policies.update');
    Route::delete('clients/{client}/policies/{policy}', [ClientController::class, 'destroyPolicy'])->name('clients.policies.destroy');

    // Leads
    Route::resource('leads', LeadController::class)->except(['show']);
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    // Touchpoints
    Route::get('follow-up', [TouchpointController::class, 'index'])->name('touchpoints.index');
    Route::post('clients/{client}/touchpoints', [TouchpointController::class, 'storeForClient'])->name('clients.touchpoints.store');
    Route::post('leads/{lead}/touchpoints', [TouchpointController::class, 'storeForLead'])->name('leads.touchpoints.store');

    // Reach Angles
    Route::resource('angles', ReachAngleController::class);
    Route::get('content-library', [ReachAngleController::class, 'library'])->name('angles.library');
    Route::post('angles/{angle}/clients/{client}', [ReachAngleController::class, 'attachClient'])->name('angles.attach');
    Route::post('angles/{angle}/generate', [ReachAngleController::class, 'generate'])->name('angles.generate');
    Route::patch('angle-contents/{content}/pin', [ReachAngleController::class, 'pin'])->name('angle-contents.pin');

    // Settings — Plan Product Catalog
    Route::resource('plan-products', PlanProductController::class)->except(['show']);

    // Settings — API
    Route::get('settings/api', [SettingController::class, 'api'])->name('settings.api');
    Route::post('settings/api', [SettingController::class, 'updateApi'])->name('settings.api.update');

    // Admin — users + invitations
    Route::middleware(EnsureIsAdmin::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::patch('users/{user}/toggle', [AdminController::class, 'toggleActive'])->name('users.toggle');
        Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
        Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
        Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    });

});

// Invite registration (guest only)
Route::middleware('guest')->group(function () {
    Route::get('invite/{token}', [InvitationController::class, 'show'])->name('invite.show');
    Route::post('invite/{token}', [InvitationController::class, 'register'])->name('invite.register');
});

require __DIR__.'/auth.php';
