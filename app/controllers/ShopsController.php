<?php
class ShopsController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Shop Management', URL::to('shops'));
    }

    public function getIndex()
    {
		$shopData = Shop::get();
        $this->data['shopData'] = $shopData;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Shops Management');

        return $this->theme->of('shops.index', $this->data)->render();
    }

    public function getEdit($id = 0)
    {
        $shopData = Shop::orderBy('shop_id', 'desc')->findOrFail($id);

        $this->data['shop'] = $shopData;
        $this->data['formData'] = array(
            'shop_id'       => $shopData->shop_id,
            'name'          => $shopData->name
        );

        $this->theme->breadcrumb()->add('Edit Shop', URL::to('shops/edit/'.$id));
        $this->theme->setTitle('Edit Shop');

        return $this->theme->of('shops.edit', $this->data)->render();
    }

    public function postEdit($id)
    {
		$shopData = Shop::findOrFail($id);
        $shopData->name = Input::get('name');

        if(Input::get('translate')) {
            $translate = Input::get('translate');

            if (!empty($translate['name']))
            {
                $shopData->setTranslate('name', $translate['name']);
            }

        }

        if ( !$shopData->save() )
        {
            return Redirect::to('shops/edit/'.$id)->withInput()->withErrors($shopData->errors());
        }

        return Redirect::to('shops');
    }

}