<?php
/**
 *	@author Preme W. <preme_won@Truecorp.co.th>
 *	@desc  Banner Group Management 
 *	@since   Jan 14, 2014
 *	@version  1.0
 *	@package  PCMS 
 *
 */
class BannerGroupController extends AdminController {

	public function getIndex()
	{
        $args = array();
        $data = array(); 
        $name = Input::get('search_name');
        $banner_position_id = Input::get('banner_position_id');

        #$this->theme->breadcrumb()->add('Banners Management', URL::to('banners'));
        $this->theme->breadcrumb()->add('Banner Management', URL::to('banners/groups'));

        #$banner_groups = new BannerGroup; 
        if ( ! empty($name) && ! empty($banner_position_id))
        {
        	$data['groups'] = BannerGroup::where('name', 'LIKE', '%'.$name.'%')
        					->where('banner_position_id', '=', $banner_position_id)
        					->orderBy('name', 'asc')
        					->paginate(10);

        }
        elseif ( ! empty($banner_position_id))
        {
        	#$banner_groups->where('banner_position_id', '=', $banner_position_id);
        	$data['groups'] = BannerGroup::where('banner_position_id', '=', $banner_position_id)
        									->orderBy('name', 'asc')
        									->paginate(10);
        }
        elseif ( ! empty($name))
        {
        	$data['groups'] = BannerGroup::where('name', 'LIKE', '%'.$name.'%')
        									->orderBy('name', 'asc')
        									->paginate(10);
        }

		#dd(DB::getQueryLog());

        if (empty($name) && empty($banner_position_id))
        {
        	$data['groups'] = BannerGroup::orderBy('name', 'asc')->paginate(10);
        }

        $data['name'] = $name;
        $data['banner_position_id'] = $banner_position_id;

        $this->theme->asset()->usePath()->add('banner-group-css', 'admin/css/pagination.css');
		$this->theme->asset()->container('footer')->usePath()->add('banner-group-js', 'admin/js/banner.js');
        
		return $this->theme->of('banners.groups.list', $data)->render();
	}

	public function postIndex()
	{
		return "Post Index";
	}

	public function getCreate()
	{
		$view_data = array();

        $this->theme->breadcrumb()->add('Banner Management', URL::to('banners/groups'));
        $this->theme->breadcrumb()->add('Create Banner Groups', URL::to('banners/groups/create'));
		return $this->theme->of('banners.groups.create', $view_data)->render();
	}
    
    public function getUpdate($id = NULL)
    {
        $view_data = array();
        $this->theme->breadcrumb()->add('Banner Management', URL::to('banners/groups'));
        $this->theme->breadcrumb()->add('Edit Banner Groups', URL::to('banners/groups/update'));

        $groups = BannerGroup::find($id);
        $view_data = compact('groups');

		return $this->theme->of('banners.groups.update', $view_data)->render();   
        
    }

    public function getDelete($id)
    {
    	$group = BannerGroup::find($id);
    	if ($group->delete())
    	{
    		return Redirect::back()->withSuccess('Deleted group Successfully');
    	}

    	return Redirect::back()->withErrors($group->getErrors());
    }

	public function postCreate()
	{
		$group = new BannerGroup;
		$group->name = Input::get('name');
		$group->banner_position_id = Input::get('banner_position_id');
		$group->description = Input::get('description');
		$group->status_flg = Input::get('status_flg');
		$group->is_random = (Input::get('is_random') != "") ? Input::get('is_random') : "N"; 
		$group->show_per_time = Input::get('show_per_time');

		// $image = Input::file('image');
		// $attachment = UP::inject(array('subpath'=>function()
		// {
		// 	return 'uploads'.DIRECTORY_SEPARATOR.'banners'.DIRECTORY_SEPARATOR.date('y-m-j');
		// }))->upload(BannerSection::find(1), $image)->getMasterResult();

		if ( ! $group->save())
		{
			return Redirect::back()->withInput()->withErrors($group->getErrors());
		}

		return Redirect::to('banners/groups')->withSuccess('Created Groups Successfully');
	}

	public function postUpdate($id)
	{
		$group = BannerGroup::find($id);
		$group->name = Input::get('name');
		$group->banner_position_id = Input::get('banner_position_id');
		$group->description = Input::get('description');
		$group->status_flg = Input::get('status_flg');
		$group->is_random = (Input::get('is_random') != "") ? Input::get('is_random') : "N"; 
		$group->show_per_time = Input::get('show_per_time');

		if ( ! $group->save())
		{
			return Redirect::back()->withInput()->withErrors($group->getErrors());
		}

		return Redirect::back()->withSuccess('Updated Groups Successfully');	
	}

	public function postDelete()
	{

	}

}