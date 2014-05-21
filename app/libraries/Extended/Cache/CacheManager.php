<?php namespace Extended\Cache;

use Illuminate\Support\Facades\Route as Route;
use Illuminate\Support\Facades\Request as Request;
use Illuminate\Cache\CacheManager as BaseCacheManager;
use Illuminate\Support\Facades\Cache as Cache;

class CacheManager extends BaseCacheManager {

	protected $apiCacheKey;

	protected $apiSection;

	public function __construct($app)
	{
		parent::__construct($app);

		$this->apiCacheKey = $this->getApiCacheKey();
		$this->apiSection = $this->getApiSection();
	}

	public function getCurrentApiData()
	{
		$data = Cache::tags($this->apiSection)->get($this->apiCacheKey);

		return $data;
	}

	public function setCurrentApiData($data = array(), $timeout = 5)
	{
		Cache::tags($this->apiSection)->put($this->apiCacheKey, $data, $timeout);
	}

	public function getApiCacheKey()
	{
		$cacheKey = Request::path();
		$query = urldecode(Request::getQueryString());

		if ( !empty($query) )
		{
			$cacheKey .= '?' . $query;
		}

		return $cacheKey;
	}

	public function getApiSection()
	{
		// $routeAction = Route::currentRouteAction();

		$currentRoute = Route::getCurrentRoute();

		$routeAction = $currentRoute ? $currentRoute->getAction() : null;

		$routeAction = array_get($routeAction, 'controller');

		$sectionName = '';

		switch ($routeAction)
		{
			case "ApiCollectionsController@getListProducts" :
			case "ApiBrandsController@getListProducts" :
				$sectionName = 'products';
				break;
			case "ApiCollectionsController@getListBrands" :
				$sectionName = 'brands';
				break;
			default :
				break;
		}

		if ( $sectionName != '' )
		{
			return $sectionName;
		}

		$controllerName = strstr($routeAction, '@', true);

		switch ($controllerName)
		{
			case "ApiProductSearchController" :
			case "ApiProductsController" :
				$sectionName = 'products';
				break;
			case "ApiCollectionsController" :
				$sectionName = 'collections';
				break;
			case "ApiBrandsController" :
				$sectionName = 'brands';
				break;
			default :
				$sectionName = 'others';
				break;
		}

		return $sectionName;
	}




	public function getDataByPkey($pkey)
	{

	}

}