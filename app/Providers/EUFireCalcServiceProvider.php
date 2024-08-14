<?php

namespace App\Providers;

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
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}