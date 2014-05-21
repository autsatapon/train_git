<?php
/**
 *	@author  Preme W. <preme_won@truecorp.co.th>
 *	@since    Jan 21, 2014
 *	@
 *
 */
class BannersController extends AdminController {

	public $banner_type = array(
		1 => 'Link',
		2 => 'Map Area',
		3 => 'Youtube Embed'
	);

	public function getIndex()
	{
		$data = array();
		$banner_group_id = Input::get('banner_group_id');

		if (Input::has('banner_group_id') == false)
		{
			return Redirect::to('banners/groups');
		}
		$banner_group = BannerGroup::find($banner_group_id);
		if (empty($banner_group['id']))
		{
			return Redirect::to('banners/groups');
		}

		//--- Get List of banner by banner_group_id ---//
		if (Input::has('keyword'))
		{
			$data['banners'] = Banner::where('banner_group_id', '=', $banner_group_id)->where('name', 'LIKE', '%'.Input::get('keyword').'%')->paginate(10);
		}
		else
		{
			$data['banners'] = Banner::where('banner_group_id', '=', $banner_group_id)->paginate(10);
		}
		//--- Get banner groups detail (Including banner_position_id) ---//
		$banner_groups = BannerGroup::find($banner_group_id);
		//--- Assign banner_position_id pass to view ---//
		$data['banner_position_id'] = $banner_groups['banner_position_id'];

		//--- Find banner_groups under position ---//
		$data['banner_groups'] = BannerPosition::find($data['banner_position_id'])->bannerGroups;
		$data['banner_type'] = $this->banner_type;

		$this->theme->breadcrumb()->add('Banner Management', URL::to('banners/groups'));
		$this->theme->breadcrumb()->add($data['banner_groups']->first()->name);


		//--- Added Javascript and css to layouts ---//
		$this->theme->asset()->usePath()->add('banner-group-css', 'admin/css/pagination.css');
		$this->theme->asset()->container('footer')->usePath()->add('helper-js', 'admin/js/helper.js');
		$this->theme->asset()->container('footer')->usePath()->add('banner-js', 'admin/js/banner-management.js');

		$data['banner_group_id'] = Input::get('banner_group_id');
		return $this->theme->of('banners.list', $data)->render();
	}

	/**
	 * Create Banner form
	 * @param $banner_group_id
	 * @return HTML
	**/
	public function getCreate($banner_group_id='')
	{
		$data = array();
		if (empty($banner_group_id)) echo 'redirect';
		$data['groups'] = BannerGroup::find($banner_group_id);

		if(empty($data['groups']))
		{
			return Redirect::to('banners/groups');
		}

		$this->theme->breadcrumb()->add('Banner Management', URL::to('banners/groups'));
		$this->theme->breadcrumb()->add($data['groups']->name, URL::to('banners?banner_group_id='.$banner_group_id));
		$this->theme->breadcrumb()->add('Banner Add');

		$this->theme->asset()->usePath()->add('img-map-css', 'admin/css/imgmap.css');
		$this->theme->asset()->usePath()->add('banner-css', 'css/banners.css');

		$this->theme->asset()->container('footer')->usePath()->add('colorPicker', 'js/banner/jquery.colorPicker.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('jquery.maphilight', 'js/banner/jquery.maphilight.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('imagemap', 'js/banner/imagemap/imgmap.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('default_interface', 'js/banner/imagemap/default_interface.js', 'jquery');

		$this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
    	$this->theme->asset()->container('footer')->usePath()->add('jquery-ui-timepicker', 'jui/js/timepicker/jquery-ui-timepicker.min.js', 'jquery');
    	$this->theme->asset()->container('footer')->usePath()->add('promotions-create', 'admin/js/promotions_create.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('banners-create', 'js/banner/banners_create.js', 'jquery');

		return $this->theme->of('banners.create', $data)->render();
	}

	public function missingMethod($parameters = array())
	{
		#echo '====';
    	#d($parameters);
	}

	public function postCreate()
	{
		$banner = new Banner;
		$banner->banner_group_id = Input::get('banner_group_id');
		$banner->name = Input::get('name');
		$banner->type = Input::get('banner_type');


		$banner->url_link = Input::get('link');
		$banner->youtube_embed = Input::get('youtube_embed');
		$banner->description = Input::get('description');
		$banner->target = Input::get('target');
		$banner->status_flg = Input::get('cstatus');

		$period_time 	= Input::get('period_time');
		$start_date 	= Input::get('start_date');
		$end_date 		= Input::get('end_date');

		if(!empty($period_time))
		{
			if(empty($start_date))
			{
				return Redirect::back()->withInput()->withErrors('Start Date is Require');
			}
			elseif(empty($end_date))
			{
				return Redirect::back()->withInput()->withErrors('End Date is Require');
			}
			else
			{
				$banner->period = 'Y';
				$banner->effectived_at = Input::get('start_date').' 00:00:00';
				$banner->expired_at = Input::get('end_date').' 23:59:59';
			}
		}
		else
		{
			$banner->period = 'N';
		}

		if($banner->type == 1)
		{
			$banner->addValidate(
				array('image' => Input::file('banner_image')),
				array('image' => 'image')
			);

			if(empty($banner->url_link))
			{
				return Redirect::back()->withInput()->withErrors('Link URL is Require');
			}
		}
		elseif($banner->type == 2)
		{

			$banner->img_path = Input::get('hid_img_path');
			if(empty($banner->img_path))
			{
				return Redirect::back()->withInput()->withErrors('IMG path map area is Require.');
			}
		}
		elseif($banner->type == 3)
		{
			if(empty($banner->youtube_embed))
			{
				return Redirect::back()->withInput()->withErrors('Youtube Embed is Require');
			}
		}


		if(! $banner->save())
		{
			return Redirect::back()->withInput()->withErrors($banner->getErrors());
		}
		if($banner->type == 2)
		{
			$img = Input::get('img');

			foreach($img as $image_key => $image)
			{
				if(!empty($image['img_coords']))
				{
					$banner_has_product = new BannerHasProduct;
					$banner_has_product->banner_id = $banner->id;
					$banner_has_product->product_id = $image['img_pid'];
					$banner_has_product->map_position = $image['img_coords'];
					$banner_has_product->url_link = $image['img_href'];
					$banner_has_product->tag_alt = $image['img_alt'];

					$banner_has_product->save();
				}
			}
		}

		if(Input::hasFile('banner_image'))
		{
			$fileExt = Input::file('banner_image')->getClientOriginalExtension();

			$real_path = Config::get('up::uploader.baseDir');
			$destinationPath = '/banners/'.$banner->banner_group_id;
			// $destinationPath = '/uploads/banners/'.$banner->banner_group_id;
			$fileName = $banner->id.'.'.$fileExt;
			$path = $destinationPath.'/'.$fileName;

			#upload file
			$move = Input::file('banner_image')->move($real_path.$destinationPath, $fileName);

			#get height width
			$image_size = getimagesize($real_path.$path);
			$banner->width = $image_size[0];
			$banner->height = $image_size[1];

			$banner->img_path = $path;
			$banner->save();

			/* $attachment = UP::inject(array('subpath'=>function()
			{
				return 'uploads'.DIRECTORY_SEPARATOR.'banners'.DIRECTORY_SEPARATOR.date('y-m-j');
			}))->upload(Banner::find($banner->id), Input::file('banner_image'))->getMasterResult();

			$banner->attachment_id = $attachment['fileName'];
            $banner->save(); */
		}

		return Redirect::to('banners?banner_group_id='.$banner->banner_group_id)->withSuccess('Created Banners Successfully');
	}

	public function getIframe($banner_id = "")
	{
		$this->theme = Theme::uses('admin')->layout('admin-blank');
		$data = array();

		if( ! empty($banner_id))
		{
			$data = Banner::find($banner_id);
			//echo 'test';
			$path = $data->img_path;
			$filename = explode("/",$path);
			$url = Config::get('up::uploader.baseUrl');

			echo "Uploaded
			<span id='src' data-path='".$path."'rel='".$url.'/'.$path."'>
				{$filename[(count($filename)- 1)]}
			</span>
			- <a href='javascript:history.go(-1)'>upload another</a>";
		}
		$this->theme->asset()->usePath()->add('banner-css', 'css/banners.css');
		return $this->theme->of('banners.upload', $data)->render();
	}

	public function postIframe()
	{
		$this->theme = Theme::uses('admin')->layout('admin-blank');
		#d(Input::file('file_src'));
		if ( Input::file('file_src') )
		{
			$fileExt = Input::file('file_src')->getClientOriginalExtension();
			$url = Config::get('up::uploader.baseUrl');
			$real_path = Config::get('up::uploader.baseDir');
			$destinationPath = '/banners/maparea';
			// $destinationPath = '/uploads/banners/maparea';
			$fileName = 'map_'.date('ymdHis').'.'.$fileExt;
			$path = $destinationPath.'/'.$fileName;

			#upload file
			$move = Input::file('file_src')->move($real_path.$destinationPath, $fileName);
			#d($move);
			echo "Uploaded
			<span id='src' data-path='".$path."'rel='".$url.$path."'>
				{$fileName}
			</span>
			- <a href='javascript:history.go(-1)'>upload another</a>";
		}
		$data = array();
		return $this->theme->of('banners.upload', $data)->render();
	}

	public function getUpdate($banner_id='')
	{
		$data = array();
		$groups = NULL;
		$position = NULL;
		$banner_group_id = '';
		if (empty($banner_id)) return Redirect::back();

		$data['banner'] = Banner::find($banner_id);
		if (empty($data['banner']))
		{
			return Redirect::back()->withErrors('Banner not found.');
		}
		else
		{
			$groups = BannerGroup::find($data['banner']['banner_group_id']);
			$position = BannerPosition::find($groups['banner_position_id']);
			$banner_group_id = $data['banner']['banner_group_id'];
		}
		$data['position'] = $position;
		$data['groups'] = $groups;

		# Get Banner has product [Case maparea]
		$banner_has_product = BannerHasProduct::where('banner_id', $banner_id)->get();
		if(!empty($banner_has_product))
		{
			$map_text = '<map id="imgmap2014127223432" name="imgmap2014127223432">';
			foreach($banner_has_product as $bhp)
			{
				$map_text .= '<area shape="rect" pid="'.$bhp->product_id.'" alt="'.$bhp->tag_alt.'" title="" coords="'.$bhp->map_position.'" href="'.$bhp->url_link.'" target="" />';
			}
			$map_text .= '<area shape="rect" alt="" title="" coords="0,0,0,0" href="" target="" /></map>';
		}

		$data['banner_map_code'] = $map_text;

		$this->theme->breadcrumb()->add('Banner Management', URL::to('banners/groups'));
		$this->theme->breadcrumb()->add($data['groups']->name, URL::to('banners?banner_group_id='.$banner_group_id));
		$this->theme->breadcrumb()->add('Banner Update');

		$this->theme->asset()->usePath()->add('img-map-css', 'admin/css/imgmap.css');
		$this->theme->asset()->usePath()->add('banner-css', 'css/banners.css');

		$this->theme->asset()->container('footer')->usePath()->add('colorPicker', 'js/banner/jquery.colorPicker.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('jquery.maphilight', 'js/banner/jquery.maphilight.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('imagemap', 'js/banner/imagemap/imgmap.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('default_interface', 'js/banner/imagemap/default_interface.js', 'jquery');

		$this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
    	$this->theme->asset()->container('footer')->usePath()->add('jquery-ui-timepicker', 'jui/js/timepicker/jquery-ui-timepicker.min.js', 'jquery');
    	$this->theme->asset()->container('footer')->usePath()->add('promotions-create', 'admin/js/promotions_create.js', 'jquery');
		$this->theme->asset()->container('footer')->usePath()->add('banners-create', 'js/banner/banners_create.js', 'jquery');

		//sd($data['groups']);
		return $this->theme->of('banners.update', $data)->render();
	}

	public function postUpdate($banner_id='')
	{
		# Check Image update
		$banner = Banner::find($banner_id);
		$banner->banner_group_id = Input::get('banner_group_id');
		$banner->name = Input::get('name');
		$banner->type = Input::get('banner_type');

		$banner->url_link = Input::get('link');
		$banner->youtube_embed = Input::get('youtube_embed');
		$banner->description = Input::get('description');
		$banner->target = Input::get('target');
		$banner->status_flg = Input::get('cstatus');

		$period_time 	= Input::get('period_time');
		$start_date 	= Input::get('start_date');
		$end_date 		= Input::get('end_date');

		if(!empty($period_time))
		{
			$banner->period = 'Y';
			if(empty($start_date))
			{
				return Redirect::back()->withInput()->withErrors('Start Date is Require');
			}
			elseif(empty($end_date))
			{
				return Redirect::back()->withInput()->withErrors('End Date is Require');
			}
			else
			{
				$banner->effectived_at = Input::get('start_date').' 00:00:00';
				$banner->expired_at = Input::get('end_date').' 23:59:59';
			}
		}
		else
		{
			$banner->period = 'N';
			$banner->effectived_at = '0000-00-00 00:00:00';
			$banner->expired_at = '0000-00-00 00:00:00';
		}

		#Check banner type [1 = Link | 2 = Map area | 3 = Embed]
		if($banner->type == 1)
		{
			if(Input::hasFile('banner_image'))
			{
				$banner->addValidate(
					array('image' => Input::file('banner_image')),
					array('image' => 'image')
				);

				if(empty($banner->url_link))
				{
					return Redirect::back()->withInput()->withErrors('Link URL is Require');
				}
			}
		}
		elseif($banner->type == 2)
		{
			$BannerHasProduct = BannerHasProduct::where('banner_id', $banner_id);
			$BannerHasProduct->delete();
			$banner->img_path = Input::get('hid_img_path');
			if(empty($banner->img_path))
			{
				return Redirect::back()->withInput()->withErrors('IMG path map area is Require.');
			}
		}
		elseif($banner->type == 3)
		{
			if(empty($banner->youtube_embed))
			{
				return Redirect::back()->withInput()->withErrors('Youtube Embed is Require');
			}
		}

		# Save data to DB
		if(! $banner->save())
		{
			return Redirect::back()->withInput()->withErrors($banner->getErrors());
		}

		if($banner->type == 2)
		{
			$img = Input::get('img');

			foreach($img as $image_key => $image)
			{
				if(!empty($image['img_coords']))
				{
					//echo $image['img_coords'];
					if(trim($image['img_coords']) != "0,0,0,0")
					{
						$banner_has_product = new BannerHasProduct;
						$banner_has_product->banner_id = $banner->id;
						$banner_has_product->product_id = $image['img_pid'];
						$banner_has_product->map_position = $image['img_coords'];
						$banner_has_product->url_link = $image['img_href'];
						$banner_has_product->tag_alt = $image['img_alt'];

						$banner_has_product->save();
					}
				}
			}
			//exit();
		}
		# Upload file
		if(Input::hasFile('banner_image'))
		{
			$fileExt = Input::file('banner_image')->getClientOriginalExtension();

			$real_path = Config::get('up::uploader.baseDir');

			$destinationPath = '/banners/'.$banner->banner_group_id;
			// $destinationPath = '/uploads/banners/'.$banner->banner_group_id;
			$fileName = $banner->id.'.'.$fileExt;
			$path = $destinationPath.'/'.$fileName;

			#upload file
			$move = Input::file('banner_image')->move($real_path.$destinationPath, $fileName);

			#get height width
			$image_size = getimagesize($real_path.$path);
			$banner->width = $image_size[0];
			$banner->height = $image_size[1];

			$banner->img_path = $path;
			$banner->save();
		}

		return Redirect::to('banners/update/'.$banner->id)->withSuccess('Created Banners Successfully');
	}

	public function getDelete($id)
	{
		$banners = Banner::find($id);

		#$banner_group_id = $banners->banner_group_id;

		#d($banner_group_id);
		#exit;
		#exit;

		if ($banners->delete())
		{
			return Redirect::back()->withSuccess('Deleted banners successfully');
		}
		return Redirect::to('banners')->withErrors("Can't delete banner");
	}

}