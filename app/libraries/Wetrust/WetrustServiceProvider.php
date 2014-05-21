<?php namespace Wetrust;

use Illuminate\Support\ServiceProvider;

class WetrustServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $path = app_path('libraries/Wetrust');

        $this->package('wetrust/wetrust', 'wetrust', $path);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerWetrust();
    }
	

    protected function registerWetrust()
    {
        $this->app['wetrust'] = $this->app->share(function($app)
        {
            //$config = $app['config']->get('wetrust::config');

            return new Wetrust($app['config'], $app['api']);
        });
    }



    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('wetrust');
    }

}