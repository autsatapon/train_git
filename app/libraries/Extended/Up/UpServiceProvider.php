<?php namespace Extended\Up;

use Teepluss\Up\UpServiceProvider as BaseUpServiceProvider;

class UpServiceProvider extends BaseUpServiceProvider {

    /**
     * Register package.
     *
     * @return void
     */
    public function boot()
    {
        $path = base_path('vendor/teepluss/up/src');

        $this->package('teepluss/up', 'up', $path);
    }

    /**
     * Register uploader adapter.
     *
     * @return void
     */
    public function registerUploader()
    {
        $this->app['up.uploader'] = $this->app->share(function($app)
        {
            return new Uploader($app['config'], $app['request'], $app['files']);
        });
    }

    /**
     * Register core class.
     *
     * @return void
     */
    protected function registerUp()
    {
        $this->app['up'] = $this->app->share(function($app)
        {
            $app['up.loaded'] = true;

            return new Up($app['config'], $app['up.attachment'], $app['up.uploader']);
        });
    }

}