<?php

class ApiCustomersController extends ApiBaseController {

    private $order;

    public function __construct(OrderRepositoryInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @api {get} /customers/address Get saved customer's address
     * @apiName Get customer's address
     * @apiGroup Customers
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     *
     * @apiSuccess {Array} data List of addresses.
     */
    public function getAddress(PApp $papp)
    {
        $customerRefId = Input::get('customer_ref_id');

        $customerAddresses = CustomerAddress::whereAppId($papp->getKey())->whereCustomerRefId($customerRefId)->get();

        return API::createResponse($customerAddresses, 200);
    }

    /**
     * @api {post} /customers/address Insert or update customer's address
     * @apiName Insert or update customer's address
     * @apiDesciption Insert if id is not provided.
     * @apiGroup Customers
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} name Customer Name.
     * @apiParam {String} email Customer Email.
     * @apiParam {String} address Customer Address.
     * @apiParam {Number} district_id Customer District Id.
     * @apiParam {Number} city_id Customer City Id.
     * @apiParam {Number} province_id Customer Province Id.
     * @apiParam {String} phone Customer Phone number.
     * @apiParam {String} postcode Customer Postcode.
     * @apiParam {Number} [id] Address id (if 0 or not specific = create new address).
     *
     * @apiSuccess {Array} data Address Information.
     */
    public function postAddress(PApp $papp)
    {
        if (Input::get('id', 0) == 0)
        {
            $address = new CustomerAddress;
            $address->app_id = $papp->getKey();
            $address->customer_ref_id = Input::get('customer_ref_id');
        }
        else
        {
            $address = CustomerAddress::where('customer_ref_id', Input::get('customer_ref_id'))->where('app_id', $papp->getKey())->where('id', Input::get('id'))->first();

            if (is_null($address))
            {
                $address = new CustomerAddress;
                $address->app_id = $papp->getKey();
                $address->customer_ref_id = Input::get('customer_ref_id');
            }
        }

        $address->name = Input::get('name');
        $address->email = Input::get('email');
        $address->address = Input::get('address');
        $address->province_id = Input::get('province_id');
        $address->city_id = Input::get('city_id');
        $address->district_id = Input::get('district_id');
        $address->postcode = Input::get('postcode');
        $address->phone = Input::get('phone');

        $address->save();

        return API::createResponse($address, 200);
    }

    /**
     * @api {get} /customers/trueyou Get is thai_id trueyou?
     * @apiName Get trueyou
     * @apiGroup Customers
     *
     * @apiParam {String} thai_id Thai ID.
     *
     * @apiSuccess {Array} data contains card which is red black or empty.
     */
    public function getTrueyou()
    {
        $thai_id = Input::get('thai_id');

        if (!$thai_id)
        {
            return API::createResponse('Error, Cannot validate (thai_id is required).', 400);
        }

        $trueCard = App::make('truecard');
        $result = $trueCard->getInfoByThaiId($thai_id)->check();

        return API::createResponse(array('card' => $result), 200);
    }

    /**
     * @api {get} /customers/orders Get Orders of Customer
     * @apiName Get customer's orders
     * @apiGroup Orders
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     *
     * @apiSuccess {Array} data List of orders.
     */
    public function getOrders(PApp $app)
    {
        $customer_ref_id = Input::get('customer_ref_id');
        $limit = Input::get('limit', 10);
        $page = Input::get('page', 1);

        if (!$customer_ref_id)
        {
            return API::createResponse('Error, Cannot fetch orders (customer_ref_id is required).', 400);
        }

        $data = $this->order->getOrderByCustomerRefId($app->getKey(), $customer_ref_id, $limit, $page);

        return API::createResponse($data, 200);
    }

}
