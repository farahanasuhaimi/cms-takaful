<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Policy;
use App\Observers\ClientObserver;
use App\Observers\LeadObserver;
use App\Observers\PolicyObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Client::observe(ClientObserver::class);
        Lead::observe(LeadObserver::class);
        Policy::observe(PolicyObserver::class);
    }
}
