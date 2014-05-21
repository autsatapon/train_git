<?php
class BannerPositionController extends AdminController {

	public function anyGroups()
	{
		if (Input::get('isAjax') == true)
		{
			$banner_position_id = Input::get('banner_position_id');
			$banner_groups = BannerGroup::where('status_flg', 'Y')->where('banner_position_id', $banner_position_id)->get();

			$json = array();
			#sd($banner_groups);
			if ( ! empty($banner_groups))
			{
				foreach ($banner_groups as $key => $value)
				{

					$json[] = array('opt_value' => $value['id'], 'opt_text' => $value['name']);
				}
			}
			echo json_encode($json);
		}
	}
}