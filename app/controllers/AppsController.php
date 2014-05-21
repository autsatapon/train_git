<?php

class AppsController extends AdminController {

	public function __construct()
	{
		parent::__construct();

		$this->theme->breadcrumb()->add('Apps Management', URL::to('apps'));
	}

	public function getIndex()
	{
		$pcmsApps = PApp::all();
		$this->data['pcmsApps'] = $pcmsApps;

		$this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

		$this->theme->setTitle('Apps Management');
		return $this->theme->of('apps.index', $this->data)->render();
	}

	public function getCreate()
	{
		$this->data['formData'] = array(
			'name' => '',
			'url' => '',
			'foreground_url' => '',
			'accessible_ips' => '',
			'note' => '',
			'free_shipping' => '',
		    'max_cc_per_user' => ''
		);

		$this->theme->breadcrumb()->add('Create App', URL::to('apps/create'));
		$this->theme->setTitle('Create App');
		return $this->theme->of('apps.create-edit', $this->data)->render();
	}

	public function postCreate()
	{
		$pcmsApp = new PApp;
		$pcmsApp->name = Input::get('name');
		$pcmsApp->url = Input::get('url');
		$pcmsApp->foreground_url = Input::get('foreground_url');
		$pcmsApp->stock_code = Input::get('stock_code');
		$pcmsApp->nonstock_code = Input::get('nonstock_code');
		$pcmsApp->accessible_ips = Input::get('accessible_ips');
		$pcmsApp->free_shipping = Input::get('free_shipping');
		$pcmsApp->max_cc_per_user = Input::get('max_cc_per_user');

		if (!$pcmsApp->save())
		{
			return Redirect::to('apps/create')->withInput()->withErrors($pcmsApp->errors());
		}

		if (Input::get('note') != '')
		{
			$pcmsAppNote = new Note;
			$pcmsAppNote->detail = Input::get('note');
			$pcmsApp->note()->save($pcmsAppNote);
		}

		// Create new Index in Elastic Search
		$index = $pcmsApp->slug;
		$type = 'products';
		// http://pcms.alpha.itruemart.com/api-search/indexing/itruemart
		API::get("/api-search/indexing/{$index}", array());

		// Create Mapping Products in Elastic Search
		// http://pcms.alpha.itruemart.com/api-search/indexing/itruemart/products
		API::get("/api-search/indexing/{$index}/{$type}", array());



		return Redirect::to('apps');
	}

	public function getEdit($id = 0)
	{
		$pcmsApp = PApp::with('note')->findOrFail($id);

		$this->data['formData'] = array(
			'name' => $pcmsApp->name,
			'url' => $pcmsApp->url,
			'foreground_url' => $pcmsApp->foreground_url,
			'stock_code' => $pcmsApp->stock_code,
			'nonstock_code' => $pcmsApp->nonstock_code,
			'accessible_ips' => $pcmsApp->accessible_ips,
			'note' => (empty($pcmsApp->note)) ? '' : $pcmsApp->note->detail ,
			'free_shipping' => $pcmsApp->free_shipping ,
		    'max_cc_per_user' => $pcmsApp->max_cc_per_user
			// 'note' => $pcmsApp->note->detail
		);

		$this->theme->breadcrumb()->add('Edit App', URL::to('apps/edit/'.$id));
		$this->theme->setTitle('Edit App');
		return $this->theme->of('apps.create-edit', $this->data)->render();
	}

	public function postEdit($id = 0)
	{
		$pcmsApp = PApp::with('note')->findOrFail($id);
		$pcmsApp->name = Input::get('name');
		$pcmsApp->url = Input::get('url');
		$pcmsApp->foreground_url = Input::get('foreground_url');
		$pcmsApp->stock_code = Input::get('stock_code');
		$pcmsApp->nonstock_code = Input::get('nonstock_code');
		$pcmsApp->accessible_ips = Input::get('accessible_ips');
		$pcmsApp->free_shipping = Input::get('free_shipping');
		$pcmsApp->max_cc_per_user = Input::get('max_cc_per_user');

		if (!$pcmsApp->save())
		{
			return Redirect::to("apps/edit/{$id}")->withInput()->withErrors($pcmsApp->errors());
		}

      $papp = App::make('PAppRepositoryInterface');
      $papp->purgeCacheByPkey($pcmsApp->pkey);

		$pcmsAppNote = $pcmsApp->note;

		if ( !empty($pcmsAppNote) )
		{
			$pcmsAppNote->detail = Input::get('note');
			$pcmsAppNote->save();
		}
		else
		{
			$pcmsAppNote = new Note;
			$pcmsAppNote->detail = Input::get('note');
			$pcmsApp->note()->save($pcmsAppNote);
		}

		return Redirect::to('apps');
	}
/*
	public function getShop($appId = 0, $shopAction = 'list', $shopId = 0)
	{
		switch($shopAction)
		{
			case 'create' :
				return $this->shopCreate($appId);
			case 'edit' :
				return $this->shopEdit($appId, $shopId);
			case 'list' :
				return $this->shopList($appId);
			default :
				App::abort('404');
		}
	}

	public function postShop($appId = 0, $shopAction = 'list', $shopId = 0)
	{
		switch($shopAction)
		{
			case 'create' :
				return $this->postShopCreate($appId);
			case 'edit' :
				return $this->postShopEdit($appId, $shopId);
			case 'list' :
				return $this->postShopList($appId);
			default :
				App::abort('404');
		}
	}

	private function shopList($appId)
	{
		$pcmsApp = PApp::findOrFail($appId);

		$appShops = PAppShop::where('app_id', $appId)->get();
		$this->data['appShops'] = $appShops;
		$this->data['pcmsApp'] = $pcmsApp;

		$this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

		$this->theme->breadcrumb()->add("Manage Shops", URL::to('apps/shop/'.$appId));
		$this->theme->setTitle("Manage Shops in {$pcmsApp->name} app");
		return $this->theme->of('apps.shop.list', $this->data)->render();
	}

	private function shopCreate($appId)
	{
		$pcmsApp = PApp::findOrFail($appId);

		$this->data['formData'] = array(
			'code' => '',
			'name' => '',
			'note' => ''
		);
		$this->data['pcmsApp'] = $pcmsApp;

		$this->theme->breadcrumb()->add("Create Shop", URL::to('apps/shop/'.$appId.'/create'));
		$this->theme->setTitle("Create Shop in {$pcmsApp->name} app");
		return $this->theme->of('apps.shop.create-edit', $this->data)->render();
	}

	private function postShopCreate($appId)
	{
		$pcmsApp = PApp::findOrFail($appId);

		$appShop = new PAppShop;
		$appShop->app_id = $appId;
		$appShop->name = Input::get('name');
		$appShop->code = Input::get('code');

		if (!$appShop->save())
		{
			return Redirect::to("apps/shop/{$appId}/create")->withErrors($appShop->errors());
		}

		$appShopNote = new Note;
		$appShopNote->detail = Input::get('note');

		$appShop->note()->save($appShopNote);

		return Redirect::to("apps/shop/{$appId}");
	}

	private function shopEdit($appId, $shopId)
	{
		$appShop = PAppShop::where('app_id', $appId)->where('id', $shopId)->first();

		if (empty($appShop))
		{
			App::abort('404');
		}

		$this->data['formData'] = array(
			'code' => $appShop->code,
			'name' => $appShop->name,
			'note' => $appShop->note->detail
		);
		$this->data['pcmsApp'] = $appShop->app;

		$this->theme->breadcrumb()->add("Edit Shop", URL::to('apps/shop/'.$appId."/edit/".$shopId));
		$this->theme->setTitle("Edit Shop");
		return $this->theme->of('apps.shop.create-edit', $this->data)->render();
	}

	private function postShopEdit($appId, $shopId)
	{
		$appShop = PAppShop::where('app_id', $appId)->where('id', $shopId)->first();

		if (empty($appShop))
		{
			App::abort('404');
		}

		$appShop->name = Input::get('name');
		$appShop->code = Input::get('code');

		if (!$appShop->save())
		{
			return Redirect::to("apps/shop/{$appId}/edit/{$shopId}")->withErrors($appShop->errors());
		}

		$appShop->note->detail = Input::get('note');
		$appShop->note->save();

		return Redirect::to("apps/shop/{$appId}");
	}
*/
}