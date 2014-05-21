<?php

class ShippingMethodController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Shipping', URL::to('shipping'));
        $this->theme->breadcrumb()->add('Shipping Method', URL::to('shipping/method'));
    }

    public function getIndex()
    {
        // List All Shipping Method
        $methods = ShippingMethod::all();

        $this->data['methods'] = $methods;
        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Shipping Method Management');

        return $this->theme->of('shipping.method.index', $this->data)->render();
    }

    public function getCreate()
    {
        // Create Shipping Method Detail
        $this->data['formData'] = array(
            'name' => '',
            'description' => '',
            'tracking_url' => '',
            'max_weight' => '',
            'allow_nonstock' => '',
            'dimension_max' => '',
            'dimension_mid' => '',
            'dimension_min' => '',
            'always_with' => array(),
            'alway_with_shipping_methods' => ''
        );

        $this->data['all_methods'] = ShippingMethod::all();

        $deliveryAreas = DeliveryArea::all();
        $this->data['deliveryAreas'] = $deliveryAreas;

        $this->theme->breadcrumb()->add('Create Shipping Method', URL::to('shipping/method/create'));
        $this->theme->setTitle('Create Shipping Method');

        return $this->theme->of('shipping.method.create-edit', $this->data)->render();
    }

    public function postCreate()
    {
        // Edit Shipping Method Detail
        $method = new ShippingMethod;
        $this->prepareData($method);

        if (!$method->save())
        {
            return Redirect::to('shipping/method/create')->withInput()->withErrors($method->errors());
        }

        $deliveryAreaIdArr = Input::get('delivery_area_id');

        if (!empty($deliveryAreaIdArr))
        {
            $method->deliveryAreas()->sync($deliveryAreaIdArr);
        }

        return Redirect::to('shipping/method');
    }

    public function getEdit($id = 0)
    {
        $method = ShippingMethod::with('deliveryAreas')->findOrFail($id);

        $this->data['formData'] = array(
            'name' => $method->name,
            'description' => $method->description,
            'tracking_url' => $method->tracking_url,
            'allow_nonstock' => $method->allow_nonstock,
            'max_weight' => $method->max_weight,
            'dimension_max' => $method->dimension_max,
            'dimension_mid' => $method->dimension_mid,
            'dimension_min' => $method->dimension_min,
            'delivery_area' => $method->deliveryAreas->lists('id'),
            'always_with' => $method->alway_with_shipping_methods?explode(',', $method->alway_with_shipping_methods):array()
        );
        
        $this->data['all_methods'] = ShippingMethod::where('id', '!=', $id)->get();

        $deliveryAreas = DeliveryArea::all();
        $this->data['deliveryAreas'] = $deliveryAreas;

        $this->theme->breadcrumb()->add('Edit Shipping Method', URL::to('shipping/method/edit/'.$id));
        $this->theme->setTitle('Edit Shipping Method');

        return $this->theme->of('shipping.method.create-edit', $this->data)->render();
    }

    public function postEdit($id = 0)
    {
        $method = ShippingMethod::findOrFail($id);

        $this->prepareData($method);

        if (!$method->save())
        {
            return Redirect::to("shipping/method/edit/{$id}")->withInput()->withErrors($method->errors());
        }

        $deliveryAreaIdArr = Input::get('delivery_area_id');

        // $method->rawSoftSync( 'shipping_method_areas', 'shipping_method_id', 'delivery_area_id', $deliveryAreaIdArr );
        if (!empty($deliveryAreaIdArr))
        {
            $method->deliveryAreas()->sync($deliveryAreaIdArr);
        }
        else
        {
            $method->deliveryAreas()->detach();
        }

        return Redirect::to('shipping/method');
    }

    public function getDelete($id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->delete();

        return Redirect::back();
    }

    public function getTrashed()
    {
        //get soft deleted models.
        $methods = ShippingMethod::onlyTrashed()->get();

        //$boxes = ShippingBox::withTrashed()->get();
        $this->data['methods'] = $methods;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');
        $this->theme->breadcrumb()->add('Trashed Shipping Method', URL::to('shipping/method/trashed/'));

        return $this->theme->of('shipping.method.trashed', $this->data)->render();
    }

    public function getUndo($id = 0)
    {
        //restore a soft deleted model.
        $method = ShippingMethod::onlyTrashed()->findOrFail($id);

        $method->restore();
        return Redirect::back();
    }

    public function getDetail($shippingMethodId)
    {
        // View Shipping Method Detail
    }

    public function getSetFee($shippingMethodAreaId = 0)
    {
        // Get Shipping Fee
        $shippingFees = ShippingFee::where('shipping_method_area_id', $shippingMethodAreaId)->orderBy('weight_min', 'ASC')->get();
        $this->data['shippingFees'] = $shippingFees;

        // Get Shipping Method and Delivery Area
        $pivotModel = ShippingMethodArea::with(array('shippingMethod', 'deliveryArea'))->findOrFail($shippingMethodAreaId);
        $shippingMethod = $pivotModel->shippingMethod;
        $deliveryArea = $pivotModel->deliveryArea;
        $this->data['shippingMethod'] = $shippingMethod;
        $this->data['deliveryArea'] = $deliveryArea;

        // Get Boxes
        $shippingBoxes = ShippingBox::all();
        $this->data['shippingBoxes'] = $shippingBoxes;

        // Set Shipping Fee Table
        $this->theme->breadcrumb()->add('Set Shipping Fee', URL::to('shipping/method'));
        $this->theme->setTitle('Set Shipping Fee');
        return $this->theme->of('shipping.method.set-fee', $this->data)->render();
    }

    public function postSetFee($shippingMethodAreaId = 0)
    {
        $shippingFees = ShippingFee::where('shipping_method_area_id', $shippingMethodAreaId)->get();
        if (!$shippingFees->isEmpty())
        {
            ShippingFee::where('shipping_method_area_id', $shippingMethodAreaId)->delete();
        }

        // Get Shipping Method and Delivery Area
        $pivotModel = ShippingMethodArea::with(array('shippingMethod', 'deliveryArea'))->findOrFail($shippingMethodAreaId);
        $shippingMethod = $pivotModel->shippingMethod;
        $deliveryArea = $pivotModel->deliveryArea;

        // Get Boxes
        $shippingBoxes = ShippingBox::all();

        // Input
        $inputShippingFee = Input::get('shipping_fee');
        $inputWeightMin = Input::get('weight_min');
        $inputWeightMax = Input::get('weight_max');
        $inputBoxId = Input::get('shipping_box_id');

        $tmpWeightMax = 0;
        foreach ($inputShippingFee as $key => $val)
        {
            // Find Box.
            $box = $shippingBoxes->find($inputBoxId[$key]);

            // Add or Update Shipping Fee Table
            $fee = new ShippingFee;
            $fee->shipping_method_area_id = $shippingMethodAreaId;
            $fee->shipping_method_id = $shippingMethod->id;
            $fee->delivery_area_id = $deliveryArea->id;
            $fee->weight_min = (int) $inputWeightMin[$key];
            $fee->weight_max = isset($inputWeightMax[$key]) ? (int) $inputWeightMax[$key] : 0;
            $fee->shipping_box_id = $inputBoxId[$key];
            $fee->shipping_fee = (int) $val;
            $fee->product_weight_max = $fee->weight_max - $box->weight;
            $fee->product_weight_min = $fee->weight_min - $box->weight;

            if ($fee->product_weight_min < 0)
            {
                $fee->product_weight_min = 0;
            }

            // (unlimited max weight) if $fee->weight_max == 0 , then $fee->product_weight_max == 0
            if ($fee->product_weight_max < 0)
            {
                $fee->product_weight_max = 0;
            }

            $fee->product_weight_min = max($tmpWeightMax, $fee->product_weight_min) + 1;
            $tmpWeightMax = $fee->product_weight_max;

            if (!$fee->save())
            {
                return Redirect::to("shipping/method/set-fee/{$shippingMethodAreaId}")->withErrors($fee->errors());
            }
        }

        return Redirect::to("shipping/method/set-fee/{$shippingMethodAreaId}")->withSuccess('Set Shipping Fee Complete.');
    }

    private function prepareData($method)
    {
        $arrDimension = array('');
        $dimension = array('');

        $method->name = trim(Input::get('name'));
        $method->description = Input::get('description');
        $method->tracking_url = trim(Input::get('tracking_url')) ? Input::get('tracking_url') : null;
        $method->allow_nonstock = Input::get('allow_nonstock') == '1' ? 1 : 0;
        $method->max_weight = Input::get('max_weight');
        $method->dimension_max = Input::get('dimension_max');
        $method->dimension_mid = Input::get('dimension_mid');
        $method->dimension_min = Input::get('dimension_min');
        $method->alway_with_shipping_methods = implode(',', Input::get('always_with', array()))?:null;

        $i = 0;

        $arrDimension = array(
            '0' => $method->dimension_max,
            '1' => $method->dimension_mid,
            '2' => $method->dimension_min
        );

        arsort($arrDimension);

        foreach ($arrDimension as $k => $v)
        {
            $dimension[$i] = $v;
            $i++;
        }

        //convert kg to g.
        $method->max_weight = Input::get('max_weight') * 1000;

        // sort max mid min.
        $method->dimension_max = $dimension[0];
        $method->dimension_mid = $dimension[1];
        $method->dimension_min = $dimension[2];
        $method->dimension_min = $dimension[2];
        return $method;
    }

}

