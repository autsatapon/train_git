<?php namespace Extended\Cache;

use Illuminate\Cache\CacheServiceProvider as BaseCacheServiceProvider;

class CacheServiceProvider extends BaseCacheServiceProvider {


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['cache'] = $this->app->share(function($app)
		{
			return new CacheManager($app);
			// return new \Illuminate\Cache\CacheManager($app);
		});

		$this->app['memcached.connector'] = $this->app->share(function()
		{
			return new \Illuminate\Cache\MemcachedConnector;
		});

		$this->registerCommands();
	}

}