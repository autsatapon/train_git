<?php

class ConceptController extends BaseController {

    private $cart;
    private $checkout;
    private $theme;
    private $prefer;

    public function __construct(
    CartRepositoryInterface $cart, CheckoutRepositoryInterface $checkout
    )
    {
        parent::__construct();

        $this->theme = Theme::uses('admin')->layout('default');

//		$this->theme->asset()->add('bootstrap-css', '//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css');
//		$this->theme->asset()->container('footer')->add('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
//		$this->theme->asset()->container('footer')->add('bootstrap-js', '//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js', 'jquery');

        $this->cart = $cart;
        $this->checkout = $checkout;

        $this->prefer['app_pkey'] = '45311375168544';
        $this->prefer['app_id'] = '1';
        $this->prefer['customer_ref_id'] = '3';
        $this->prefer['customer_type'] = 'non-user';
    }

    public function getIndex()
    {
        return 'please try <a href="/concept/ordering-flow">/concept/ordering-flow</a>';
    }

    public function getOrderingFlow()
    {
        if (Input::has('action'))
        {
            return $this->postOrderingFlow();
        }

        $user = Sentry::findUserById(12);

        $variants = ProductVariant::orderBy('id', 'DESC')->get();

        $cart = $this->cart->getCart($this->prefer);

        $order = array();
        if (!empty($cart))
        {
            $order = $this->checkout->buildOrder(PApp::find($this->prefer['app_id']), $cart);
        }

        $provinces = Province::all()->lists('name', 'id');
        $provinces = array('' => 'กรุณาเลือกจังหวัด') + $provinces;
        if (isset($order['customer_province_id']))
        {
            $cities = City::whereProvinceId($order['customer_province_id'])->get()->lists('name', 'id');
            $cities = array('' => 'กรุณาเลือกอำเภอ') + $cities;
        }

        return $this->theme->of('concept.ordering-flow', compact('user', 'variants', 'cart', 'order', 'provinces', 'cities'))->render();
    }

    public function postOrderingFlow()
    {
        $action = Input::get('action');

        if ($action == 'add-to-cart')
        {
            if (Input::has('inventory_ids'))
                foreach (Input::get('inventory_ids') as $id)
                {
                    $this->prefer['inventory_id'] = $id;
                    $this->prefer['qty'] = Input::get('qty')? : 1;

                    $data = $this->prefer;

                    API::post('/api/' . $this->prefer['app_pkey'] . '/cart/add-item', $data);
                }
        }
        else if ($action == 'update-item')
        {
            $this->prefer['items'] = array();
            $this->prefer['items'][Input::get('inventory_id')] = Input::get('qty');

            $data = $this->prefer;

            API::post('/api/' . $this->prefer['app_pkey'] . '/checkout/update-items', $data);
        }
        else if ($action == 'remove-item')
        {
            $this->prefer['inventory_id'] = Input::get('inventory_id');

            $data = $this->prefer;

            API::post('/api/' . $this->prefer['app_pkey'] . '/cart/remove-item', $data);
        }
        else if ($action == 'select-province')
        {
            $this->prefer['customer_province_id'] = Input::get('customer_province_id');
            $this->prefer['customer_city_id'] = null;

            $data = $this->prefer;

            API::post('/api/' . $this->prefer['app_pkey'] . '/checkout/set-customer-info', $data);
        }
        else if ($action == 'select-city')
        {
            $this->prefer['customer_city_id'] = Input::get('customer_city_id');

            $data = $this->prefer;

            API::post('/api/' . $this->prefer['app_pkey'] . '/checkout/set-customer-info', $data);
        }
        else if ($action == 'select-shipment')
        {
            $this->prefer['shipments'] = Input::get('shipments');

            $data = $this->prefer;

            API::post('/api/' . $this->prefer['app_pkey'] . '/checkout/select-shipment-methods', $data);
        }
        else if ($action == 'select-payment')
        {
            $this->prefer['payment_method'] = Input::get('payment_method');

            $data = $this->prefer;

            API::post('/api/' . $this->prefer['app_pkey'] . '/checkout/set-payment-info', $data);
        }
        else if ($action == 'create-order')
        {
            $data = $this->prefer;

            $resp = API::post('/api/' . $this->prefer['app_pkey'] . '/checkout/create-order', $data);

            return Redirect::to('/payment/process?order_id=' . $resp['data']['order_id']);
        }

        return Redirect::to('/concept/ordering-flow');
    }

    public function anyOrderThankyou()
    {
        return 'Thank you';
    }

}
