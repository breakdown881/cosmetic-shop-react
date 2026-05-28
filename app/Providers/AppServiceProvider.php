<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // BIND PSR-18 HTTP client
        $this->app->bind(ClientInterface::class, function () {
            return new GuzzleAdapter(new \GuzzleHttp\Client());
        });

        // BIND NodePoolInterface (Elasticsearch v8+ yêu cầu)
        $this->app->singleton(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOST', 'http://localhost:9200')])
                ->build();
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
