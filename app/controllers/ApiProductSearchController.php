<?php

class ApiProductSearchController extends ApiBaseController {

	public function searchResults($app)
	{
		// get search results data from cache
        $data = Cache::getCurrentApiData();

        if ( !empty($data) )
        {
            return API::createResponse($data);
        }

		// $defaultLimit = 20;
		// $defaultOffset = 0;

		$results = array();
		$hits = 0;

		$products = array();
		$page = 1;
		$total_page = 1;
		$item = 0;
		$total_item = 0;

		// if (Input::has('q'))
		// {
			// Get search results from internal api search (Elastic Search)
			// $index = 'pcms';
			$index = $app->slug;
			$type = 'products';

			$params = $this->setParams();

			$response = API::get("/api-search/search/{$index}/{$type}", $params);

			//sd($index, $type, $params, $response);

			//$response = json_decode($response, true);

			$hits = $response['data']['hits'];
			$results = $response['data']['results'];

			// Re-formatting date time
			$countItem = 0;
			foreach ($results as $key=>$val)
			{
				$results[$key]['data']['created_at'] = str_replace('T', ' ', $val['data']['created_at']);
				$results[$key]['data']['updated_at'] = str_replace('T', ' ', $val['data']['updated_at']);
				$results[$key]['data']['published_at'] = str_replace('T', ' ', $val['data']['published_at']);

				$products[] = $results[$key]['data'];
				$countItem++;
			}

			$page = floor($params['offset'] / $params['limit']) + 1;
			$total_page = ceil($hits/$params['limit']);
			$item = $countItem;
			$total_item = $hits;
		// }

		/*
		$data = array(
			'hits' => $hits,
			'results' => $results,
		);
		*/

		$data = array(
			'products' => $products,
			'page' => $page,
			'total_page' => $total_page,
			'item' => $item,
			'total_item' => $total_item,
		);

		// Set Cache data
        $timeout = ( Config::has('cache.timeout.short') ) ? 5 : Config::get('cache.timeout.short') ;
        Cache::setCurrentApiData($data, $timeout);

		return API::createResponse($data);
	}

	private function setParams()
	{
		$defaultLimit = 20;
		$defaultOffset = 0;

		$params = array(
			'limit' => Input::get('limit', $defaultLimit),
			'offset' => Input::get('offset', $defaultOffset)
		);

		if (Input::has('q'))
		{
			$params['q'] = Input::get('q');
		}

		if (Input::has('collectionKey'))
		{
			$params['collectionKey'] = Input::get('collectionKey');
		}

		if (Input::has('brandKey'))
		{
			$params['brandKey'] = Input::get('brandKey');
		}

		if (Input::has('priceMin'))
		{
			$params['priceMin'] = Input::get('priceMin');
		}

		if (Input::has('priceMax'))
		{
			$params['priceMax'] = Input::get('priceMax');
		}

		if (Input::has('orderBy'))
        {
            $params['orderBy'] = Input::get('orderBy');
        }

		if (Input::has('order'))
        {
            $params['order'] = Input::get('order');
        }

        if (Input::has('campaign'))
        {
            $params['campaign'] = Input::get('campaign');
        }

        if (Input::has('trueyou'))
        {
        	$params['trueyou'] = Input::get('trueyou');
        }

		return $params;
	}
}