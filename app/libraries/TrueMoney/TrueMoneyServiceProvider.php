<?php namespace TrueMoney;

use Illuminate\Support\ServiceProvider;

class TrueMoneyServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $path = app_path('libraries/TrueMoney');

        $this->package('truemoney/truemoney', 'truemoney', $path);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTrueMoney();
    }


    protected function registerTrueMoney()
    {
        $this->app['truemoney'] = $this->app->share(function($app)
        {
            return new TrueMoney($app['config'], $app['api']);
        });
    }



    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('truemoney');
    }

}