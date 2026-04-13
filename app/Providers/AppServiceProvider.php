<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Typesense\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function (){
            new Client([
                'api_key' => config('services.typesense.api_key'),
                'nodes' => [
                    [
                        'host' => config('services.typesense.host'),
                        'port' => config('services.typesense.port'),
                        'protocol' => config('services.typesense.protocol'),
                    ]
                ],
                'connection_timeout_seconds' => 2
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
