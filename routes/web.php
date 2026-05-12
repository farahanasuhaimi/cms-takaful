<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketplacePolicyController;
use App\Http\Controllers\MarketplaceStrategyController;
use App\Http\Controllers\StrategyController;
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

    // Account
    Route::get('account/credits', [AccountController::class, 'credits'])->name('account.credits');

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

    // Quotations
    Route::resource('quotations', QuotationController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');

    // Reach Angles
    Route::resource('angles', ReachAngleController::class);
    Route::post('angles/{angle}/leads/{lead}', [ReachAngleController::class, 'attachLead'])->name('angles.leads.attach');
    Route::delete('angles/{angle}/leads/{lead}', [ReachAngleController::class, 'detachLead'])->name('angles.leads.detach');
    Route::post('angles/{angle}/clients/{client}', [ReachAngleController::class, 'attachClient'])->name('angles.clients.attach');
    Route::delete('angles/{angle}/clients/{client}', [ReachAngleController::class, 'detachClient'])->name('angles.clients.detach');
    Route::post('angles/{angle}/strategies/{strategy}', [ReachAngleController::class, 'attachStrategy'])->name('angles.strategies.attach');
    Route::delete('angles/{angle}/strategies/{strategy}', [ReachAngleController::class, 'detachStrategy'])->name('angles.strategies.detach');

    // Settings — Plan Product Catalog
    Route::resource('plan-products', PlanProductController::class)->except(['show']);

    // Settings — API
    Route::get('settings/api', [SettingController::class, 'api'])->name('settings.api');
    Route::post('settings/api', [SettingController::class, 'updateApi'])->name('settings.api.update');

    // Marketplace — Policy (free)
    Route::get('marketplace/policies', [MarketplacePolicyController::class, 'index'])->name('marketplace.policies');
    Route::post('marketplace/policies/{product}/star', [MarketplacePolicyController::class, 'star'])->name('marketplace.policies.star');
    Route::post('marketplace/policies/{product}/import', [MarketplacePolicyController::class, 'import'])->name('marketplace.policies.import');

    // Strategy Library (user's own + platform-provided)
    Route::get('strategies', [StrategyController::class, 'index'])->name('strategies.index');
    Route::get('strategies/create', [StrategyController::class, 'create'])->name('strategies.create');
    Route::post('strategies', [StrategyController::class, 'store'])->name('strategies.store');
    Route::post('strategies/generate', [StrategyController::class, 'generate'])->name('strategies.generate');
    Route::post('strategies/store-generated', [StrategyController::class, 'storeGenerated'])->name('strategies.store-generated');
    Route::get('strategies/{strategy}', [StrategyController::class, 'show'])->name('strategies.show');
    Route::get('strategies/{strategy}/edit', [StrategyController::class, 'edit'])->name('strategies.edit');
    Route::put('strategies/{strategy}', [StrategyController::class, 'update'])->name('strategies.update');
    Route::delete('strategies/{strategy}', [StrategyController::class, 'destroy'])->name('strategies.destroy');
    Route::post('strategies/{strategy}/steps', [StrategyController::class, 'storeStep'])->name('strategies.steps.store');
    Route::put('strategies/{strategy}/steps/{step}', [StrategyController::class, 'updateStep'])->name('strategies.steps.update');
    Route::delete('strategies/{strategy}/steps/{step}', [StrategyController::class, 'destroyStep'])->name('strategies.steps.destroy');

    // Marketplace — Strategy (credits)
    Route::get('marketplace/strategies', [MarketplaceStrategyController::class, 'index'])->name('marketplace.strategies');
    Route::get('marketplace/strategies/my-listings', [MarketplaceStrategyController::class, 'myListings'])->name('marketplace.strategies.my');
    Route::post('marketplace/strategies', [MarketplaceStrategyController::class, 'store'])->name('marketplace.strategies.store');
    Route::post('marketplace/strategies/{listing}/buy', [MarketplaceStrategyController::class, 'buy'])->name('marketplace.strategies.buy');
    Route::delete('marketplace/strategies/{listing}', [MarketplaceStrategyController::class, 'destroy'])->name('marketplace.strategies.destroy');

    // Admin — users + invitations
    Route::middleware(EnsureIsAdmin::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('activity', [AdminController::class, 'activity'])->name('activity');
        Route::patch('users/{user}/toggle', [AdminController::class, 'toggleActive'])->name('users.toggle');
        Route::post('users/{user}/credits', [AdminController::class, 'addCredits'])->name('users.credits');
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
