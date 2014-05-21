<?php

class ShippingDeliveryAreaController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Shipping', URL::to('shipping'));
        $this->theme->breadcrumb()->add('Delivery Area', URL::to('shipping/delivery-area'));
    }

    public function getIndex()
    {
        $provinces = Province::with(array('deliveryArea' => function($q){ return $q->select('id', 'name'); }))->get();
        $deliveryAreas = DeliveryArea::select('id', 'name')->get();

        $this->data['provinces'] = $provinces;
        $this->data['deliveryAreas'] = $deliveryAreas;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Delivery Area');
        return $this->theme->of('shipping.delivery-area.index', $this->data)->render();

    }

    public function getProvince($provinceId = 0)
    {
        $province = Province::with(array('deliveryArea' => function($q){ return $q->select('id', 'name'); }))->findOrFail($provinceId);
        $cities = City::with(array('deliveryArea' => function($q){ return $q->select('id', 'name'); }))->where('province_id', $provinceId)->get();

        $deliveryAreas = DeliveryArea::select('id', 'name')->get();

        $this->data['cities'] = $cities;
        $this->data['province'] = $province;
        $this->data['deliveryAreas'] = $deliveryAreas;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->breadcrumb()->add($province->name, URL::to("shipping/delivery-area/province/{$province->id}"));
        $this->theme->setTitle("Delivery Area - {$province->name}");
        return $this->theme->of('shipping.delivery-area.province', $this->data)->render();
    }

    public function getSetArea($provinceId, $deliveryAreaId = 0, $cityId = 0)
    {
        $province = Province::findOrFail($provinceId);

        if ($deliveryAreaId > 0)
        {
            $deliveryArea = DeliveryArea::find($deliveryAreaId);
        }

        if (empty($deliveryArea) or $deliveryAreaId == 0)
        {
            $deliveryAreaId = NULL;
        }

        if ($cityId > 0)
        {
            $city = City::where('province_id', $provinceId)->where('id', $cityId)->first();

            if (!empty($city))
            {
                $city->delivery_area_id = $deliveryAreaId;
                $city->save();
            }

            return Redirect::to("shipping/delivery-area/province/{$provinceId}")->withSuccess('Set Delivery Area Complete.');
        }
        else
        {
            $province->delivery_area_id = $deliveryAreaId;
            $province->save();

            return Redirect::to('shipping/delivery-area/')->withSuccess('Set Delivery Area Complete.');
        }

    }

}