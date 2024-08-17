<?php

namespace App\Providers;

use App\Service\Fire\FireService;
use App\Service\Fire\FireServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Service\Taxes\TaxesService;
use App\Service\Taxes\TaxesServiceInterface;


class EUFireCalcServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FireServiceInterface::class, FireService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
