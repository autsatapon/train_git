<?php
/**
 *
 * @name Banners API file controller
 * @author x3dev
 * @since 23/01/2014 11.05
 */

class ApiBannersController extends ApiBaseController {

	var $_dateTime;
	var $_imgPathPrefix;

	 public function __construct()
    {
		$this->_dateTime = date('Y-m-d H:i:s');
		$this->_imgPathPrefix = Config::get('up::uploader.baseUrl').'/';
	}

	/**
	 * Get Banner Position by positionID
     *
     * @access	private
     * @param	$positionArray
     * @return	Array
     */
	private function bannerPosition($positionArray)
	{
		$data['listing'] = BannerPosition::whereIn('id', $positionArray)
			->where('status_flg', 'Y')
			->get();
		$data['visible'] = array('id', 'name', 'description', 'max_group_active', 'status_flg');

		return $data;
	}

	/**
	 * Get Banner Group by positionID
     *
     * @access	private
     * @param	$position_id
     * @return	Array
     */
	private function bannerGroup($position_id)
	{
		$data['listing'] = BannerGroup::where('banner_position_id', $position_id)
			->where('status_flg', 'Y')
			->orderBy('sort_by', 'asc')
			->get();
		$data['visible'] = array('id', 'pkey', 'name', 'description', 'is_random', 'status_flg', 'show_per_time', 'banner_list');

		return $data;
	}

	/**
	 * Get Banner listing by group_id
     *
     * @access	private
     * @param	$group_id
     * @return	Array
     */
	private function bannerList($group_id)
	{
		$data['listing'] = Banner::where('banner_group_id', $group_id)
			->where('status_flg', 'Y')
			->where(function($query)
			{
				$query->where('period', 'N');
				$query->orWhere(function($query2)
				{
					$dateNow = date('Y-m-d H:i:s');
					$query2->where(DB::raw("DATE_FORMAT(effectived_at, '%Y-%m-%d %H:%i:%s')"), "<=", $dateNow)
						->where(DB::raw("DATE_FORMAT(expired_at, '%Y-%m-%d %H:%i:%s')"), ">=", $dateNow);
				});
			})
			->orderBy('sort_by', 'asc')
			->get();
		//sd(DB::getQueryLog());
		$data['visible'] = array('id', 'pkey', 'banner_group_id', 'name', 'description', 'type', 'target', 'width', 'height','img_path', 'url_link', 'status_flg', 'youtube_embed', 'effectived_at', 'expired_at');

		return $data;
	}

	/**
	 * Get Map Area listing by banner_id
     *
     * @access	private
     * @param	$banner_id
     * @return	Array
     */
	private function MapAreaList($banner_id)
	{
		$data['listing'] = BannerHasProduct::where('banner_id', $banner_id)->get();
		$data['visible'] = array('id', 'pkey', 'product_id', 'map_position', 'url_link', 'tag_alt', 'created_at');

		return $data;
	}

	/**
	 * Get Banner listing by position
     *
     * @access	public
     * @param	string array key to access
     * @return	json
     */
	public function getIndex()
    {
        //Parameter
        $position = Input::get('position');
        if (empty($position)) return API::createResponse(FALSE, 404);

        $bannerRealnode = Cache::getCurrentApiData();

        if ( !empty($bannerRealnode) )
        {
			return API::createResponse($bannerRealnode);
        }

        // // Get Cache Key
        // $cacheKey = Cache::getApiCacheKey();

        // // If Cache HIT, Return Response Data.
        // if ( Cache::has($cacheKey) )
        // {
        //     $bannerRealnode = Cache::get($cacheKey);
        //     return API::createResponse($bannerRealnode);
        // }

		$bannerRealnode = array();

		$positionArray = explode('|', $position);
		$loop = 0;

		$bannerPosition = $this->bannerPosition($positionArray); //get banner position

		//Banner Position loop #1
        foreach ($bannerPosition['listing'] as $banner)
        {
            $banner->setVisible($bannerPosition['visible']);
			$bannerGroup = $this->bannerGroup($banner->id); //get banner group by positionID
			$bannerRealnode[$loop] = $banner->toArray();
			$bannerRealnode[$loop]['group_total'] = $bannerGroup['listing']->count();
			$loop2 = 0;

			//Banner Group loop #2
			foreach ($bannerGroup['listing'] as $group)
			{
				$group->setVisible($bannerGroup['visible']);
				$bannerList = $this->bannerList($group->id); //get banner list by groupID
				$bannerRealnode[$loop]['group_list'][$loop2] = $group->toArray();
				$bannerRealnode[$loop]['group_list'][$loop2]['banner_total'] = $bannerList['listing']->count();

				//Banner Listing loop #3
				if ($bannerRealnode[$loop]['group_list'][$loop2]['banner_total'] > 0)
				{
					$loop3 = 0;
					foreach ($bannerList['listing'] as $blist)
					{
						$blist->setVisible($bannerList['visible']);
						$blist->img_path = $this->_imgPathPrefix.$blist->img_path;
						$bannerRealnode[$loop]['group_list'][$loop2]['banner_list'][$loop3] = $blist->toArray();
						$mapareaList = $this->MapAreaList($blist->id);

						//Map Area Listing loop #4
						if ($mapareaList['listing']->count() > 0)
						{
							$loop4 = 0;
							foreach ($mapareaList['listing'] as $map)
							{
								$map->setVisible($mapareaList['visible']);
								$bannerRealnode[$loop]['group_list'][$loop2]['banner_list'][$loop3]['map_area'][$loop4] = $map->toArray();
								$loop4++;
							}
						}
						$loop3++;
					}
				}
				$loop2++;
			}
			$loop++;
        }

        // Set Cache Data
        if ( !empty($bannerRealnode) )
        {
            $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
            // Cache::put($cacheKey, $bannerRealnode, $timeout);
            Cache::setCurrentApiData($bannerRealnode, $timeout);
        }

		//sd($bannerRealnode);
		return API::createResponse($bannerRealnode);
	}

	/**
	 * for testing
     *
     * @access	public
     * @param	string array key to access
     * @return	json
     */
	public function getTest()
    {
		echo 1111;
		echo Config::get('img_path.img_url');
		//$positionx = '1|2|3';
		//$position = explode('|', $positionx);
		//print_r($position);
		$banner_id = 5;
		$bannerPosition = BannerHasProduct::where('banner_id', $banner_id)->get();
		//sd($bannerPosition->toArray());
		foreach ($bannerPosition as $banner)
        {
            echo $banner->map_position;
        }
		//sd(DB::getQueryLog());
	}
}