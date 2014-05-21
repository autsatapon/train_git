<?php
class ApiCartController extends ApiBaseController {

    protected $cart, $data;

    public function __construct(CartRepositoryInterface $cart)
    {
        $this->cart = $cart;

        $this->data = array(
            'customer_type'   => (Input::get('customer_type') == 'non-user') ? 'non-user' : 'user' ,
            'customer_ref_id' => Input::get('customer_ref_id'),
            // 'app_id'          => Input::get('app_id'),
            // 'inventory_id'    => Input::get('inventory_id'),
            // 'qty'             => Input::get('qty'),
            // 'cart_items'      => Input::get('cart_items')
        );

        // Required !!!
        if (strtolower(Request::method()) == 'post')
        {
            if (empty($this->data['customer_type']) or empty($this->data['customer_ref_id']))
            {
                return API::createResponse('Error, customer_type and customer_ref_id are required.', 400);
            }
        }

        App::make('PCMSPromotionCart');
    }

    /**
     * @api {get} /cart Get Cart
     * @apiName Request User Cart
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     *
     * @apiSuccess {Array} data Cart.
     */
    public function getIndex($app)
    {
        if ( ! Input::has('customer_ref_id') || ! Input::has('customer_type'))
        {
            throw new Exception('customer_ref_id and customer_type are required.');
        }

        return $this->getCustomerCart($app, Input::get('customer_ref_id'), Input::get('customer_type'));
    }

    /**
     * @api {post} /cart/add-item Add Item
     * @apiName Add item to cart.
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Number} inventory_id Inventory ID of item to be added.
     * @apiParam {Number} [qty=1] Quantity of item.
     * @apiParam {String} [type=normal] Whether the item will be pay by installment or normal.
     *
     * @apiSuccess {Array} data Modified cart.
     */
    public function postAddItem($app)
    {
        $this->data['app_id'] = $app->id;
        $this->data['qty'] = (is_numeric(Input::get('qty')) && Input::get('qty') > 0) ? Input::get('qty') : 1 ;
        $this->data['inventory_id'] = Input::get('inventory_id');
        $this->data['type'] = Input::get('type');

        // Check Remaining before Add to Cart
        try
        {
            $cart = $this->cart->addItem($this->data);

            $this->cart->reApplyTrueyou($this->data);

            $variantPrice = ProductVariant::with('activeSpecialDiscount')->where('inventory_id', $this->data['inventory_id'])->pluck('price');

            $responseData = $cart->toArray();
            $responseData['variant_price'] = $variantPrice;

            return API::createResponse($responseData);
        }
        catch (Exception $e)
        {
            return API::createResponse($e->getMessage(), 400);
        }
    }

    /**
     * @api {post} /cart/remove-item Remove Item
     * @apiName Remove item from cart
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Number} inventory_id Inventory ID of item to be removed.
     *
     * @apiSuccess {Array} data Modified cart.
     */
    public function postRemoveItem($app)
    {
        $this->data['app_id'] = $app->id;
        $this->data['inventory_id'] = Input::get('inventory_id');

        $cart = $this->cart->removeItem($this->data);

        $this->cart->reApplyTrueyou($this->data);

        if (!$cart)
        {
            return API::createResponse('Error, Cannot remove item. (inventory_id is required)', 400);
        }

        return API::createResponse($cart->toArray());
    }

    /**
     * @api {post} /cart/update-item Update Item
     * @apiName Update multiple items in cart
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Json} items Array of items and their quantity to be updated
     *     [
     *         {
     *             "inventory_id" => :inventory_id,
     *             "qty" => :qty
     *         },
     *         {
     *             "inventory_id" => :inventory_id,
     *             "qty" => :qty
     *         }
     *     ]
     *
     * @apiSuccess {Array} data Modified cart.
     */
    public function postUpdateItem($app)
    {
        $this->data['app_id'] = $app->id;

        // Input::get('items') is in json format.
        $this->data['items'] = json_decode(Input::get('items'), TRUE);
//        $this->data['items'] = Input::get('items');

        try
        {
            $cart = $this->cart->updateItem($this->data);

            $status = array_get($cart, 'status', true);

            if ( ! $status)
            {
                return API::createResponse(array_only($cart, array('message', 'variant')), 400);
            }
        }
        catch (Exception $e)
        {
            return API::createResponse($e->getMessage(), 400);
        }

        $this->cart->reApplyTrueyou($this->data);

//        if (!$cart)
//        {
//            return API::createResponse('Error, Cannot update Cart Item. (items is required)', 400);
//        }

        return API::createResponse($cart->toArray());
    }

    /**
     * @api {post} /cart/update-customer Update Customer
     * @apiName Update non-user's cart to user's cart and merge existing items in both cart
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Guest Customer ID (Non-user = random id).
     * @apiParam {String} updated_ref_id User Customer ID (User = sso id).
     *
     * @apiSuccess {Array} data Combined cart.
     */
    public function postUpdateCustomer($app)
    {
        $this->data['app_id'] = $app->id;
        $this->data['updated_ref_id'] = Input::get('new_ref_id');

        $cart = $this->cart->updateCustomer($this->data);

        if (!$cart)
        {
            return API::createResponse('Error, Cannot update customer type. (customer_ref_id and updated_ref_id are required).', 400);
        }

        return API::createResponse($cart->toArray());
    }

    public function getCustomerCart($app, $customer_ref_id = 0, $customer_type = 'user')
    {
        $this->data['customer_type'] = $customer_type;
        $this->data['customer_ref_id'] = $customer_ref_id;
        $this->data['app_id'] = $app->id;

        $cart = $this->cart->getCart($this->data);

        if (!$cart)
        {
            return API::createResponse('Error, Cannot get customer cart. (customer_ref_id and updated_ref_id are required).', 400);
        }

        return API::createResponse($cart->toArray());
    }

    /**
     * @api {post} /cart/apply-coupon Apply Coupon
     * @apiName Apply Coupon Code to Cart (one type of coupon can only be applied once)
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {String} code Coupon Code to be applied.
     *
     * @apiSuccess {Array} data Applied cart.
     */
    public function postApplyCoupon($app)
    {
        $code = Input::get('code');

        if ( ! $code)
        {
            $response = array(
                'errorCode' => '4001',
                'errorMessage' => 'Error, Cannot apply Cart (code is required).'
            );
            return API::createResponse($response, 400);
            // return API::createResponse('Error, Cannot apply Cart (code is required).', 400);
        }

        $this->data['app_id'] = $app->id;

        $result = $this->cart->applyCoupon($this->data, $code);

        if (is_array($result))
        {
            $response = array(
                'errorCode' => array_get($result, 'errorCode'),
                'errorMessage' => array_get($result, 'errorMessage')
            );
            return API::createResponse($response, 400);
        }
        else
        {
            return API::createResponse($result->toArray());
        }
    }

    /**
     * @api {post} /cart/remove-coupon Remove Coupon
     * @apiName Remove the applied coupon code
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {String} code Promotion Code to be removed.
     *
     * @apiSuccess {Array} data Applied cart.
     */
    public function postRemoveCoupon($app)
    {
        $code = Input::get('code');

        if ( ! $code)
        {
            return API::createResponse('Error, Cannot remove code on Cart (code is required).', 400);
        }

        $this->data['app_id'] = $app->id;

        $result = $this->cart->removeCoupon($this->data, $code);

        if (is_string($result))
        {
            return API::createResponse(array('message' => $result), 400);
        }
        else
        {
            return API::createResponse($result->toArray());
        }
    }

    /**
     * @api {post} /cart/apply-trueyou Apply TrueYou
     * @apiName Activate TrueYou promotion by Thai ID
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {String} thai_id Thai ID
     *
     * @apiSuccess {Array} data Applied cart.
     */
    public function postApplyTrueyou($app)
    {
        $thai_id = Input::get('thai_id');

        if ( ! $thai_id)
        {
            return API::createResponse('Error, Cannot apply Cart (thai_id is required).', 400);
        }

        $this->data['app_id'] = $app->id;
        $this->data['customer_ref_id'] = Input::get('customer_ref_id');
        $this->data['customer_type'] = Input::get('customer_type');
        $this->data['thai_id'] = $thai_id;

        $this->cart->applyTrueyou($this->data);

        // \Event::fire('TrueyouPromotion.onActivateTrueyou', array($this->cart));

        $cart = $this->cart->getCart($this->data);

        return API::createResponse($cart->toArray());
    }

    /**
     * @api {post} /cart/remove-trueyou Remove TrueYou
     * @apiName De-Activate TrueYou promotion
     * @apiGroup Cart
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     *
     * @apiSuccess {Array} data Applied cart.
     */
    public function postRemoveTrueyou($app)
    {
        $this->data['app_id'] = $app->id;

        $this->cart->removeTrueyou($this->data);

        $cart = $this->cart->getCart($this->data);

        return API::createResponse($cart->toArray());
    }

    /**
     * @api {post} /cart/merge Merge items from cart to cart
     * @apiName Merge items from cart to cart
     * @apiDescription Move items from "old" to another one, if items duplicate in new one the amount will be replaced.
     * @apiGroup Cart
     *
     * @apiParam {String} old_customer_ref_id Old Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} old_customer_type (user / non-user).
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     *
     * @apiSuccess {Array} data Applied cart.
     */
    public function postMerge($app)
    {
        if ( ! Input::has('old_customer_type') || ! Input::get('old_customer_ref_id'))
        {
            return API::createResponse('Error, Cannot apply Cart (old_customer_type and old_customer_ref_id are required).', 400);
        }

        // get old cart
        $oldData['app_id'] = $app->id;
        $oldData['customer_type'] = Input::get('old_customer_type');
        $oldData['customer_ref_id'] = Input::get('old_customer_ref_id');
        $oldCart = $this->cart->getCart($oldData);

        $newData['app_id'] = $app->id;
        $newData['customer_type'] = Input::get('customer_type');
        $newData['customer_ref_id'] = Input::get('customer_ref_id');

        foreach ($oldCart->cart_details as $item)
        {
            // prepare data for new cart
            $newData['qty'] = $item->quantity;
            $newData['inventory_id'] = $item->inventory_id;

            // remove same item
            $this->cart->removeItem($newData);

            // add item to new cart
            $this->cart->addItem($newData);
        }

        // delete old cart
        $this->cart->deleteCart($oldData);

        // get new cart
        $cart = $this->cart->getCart($newData);

        return API::createResponse($cart->toArray());
    }
    
    public function postApplyEmail($app){
        $customer_email = Input::get('customer_email');

        if ( ! $customer_email)
        {
            return API::createResponse('Error, Cannot apply Cart (customer_email is required).', 400);
        }

        $this->data['app_id'] = $app->id;
        $this->data['customer_ref_id'] = Input::get('customer_ref_id');
        $this->data['customer_type'] = Input::get('customer_type');
        $this->data['customer_email'] = $customer_email;
        $cart = $this->cart->applyEmail($this->data, 'post');
        
        return API::createResponse($cart->toArray());
    }
    
    public function postRemoveEmail($app){
        if ( ! $app )
        {
            return API::createResponse('Error, Cart not found.', 400);
        }
        $this->data['app_id'] = $app->id;
        $this->data['customer_ref_id'] = Input::get('customer_ref_id');
        $this->data['customer_type'] = Input::get('customer_type');
        $cart = $this->cart->applyEmail($this->data, 'delete');
        
        return API::createResponse($cart->toArray());
    }
	
	public function postSaveStage($app){
        $app_id 			= $app->id;
		$customer_ref_id	= Input::get('customer_ref_id');
		$stage	= Input::get('stage');
		
        if ( ! $customer_ref_id)
        {
            return API::createResponse('Error, Cannot apply Cart (customer_ref_id is required).', 400);
        }
		
		if ( ! $stage)
        {
            return API::createResponse('Error, Cannot apply Cart (stage is required).', 400);
        }
		
		$cart = new Cart;
		
		$update_param = array(
							'stage' => $stage
						);
						
		$update = Cart::where('app_id', '=', $app_id)->where('customer_ref_id', '=', $customer_ref_id)->update($update_param);
        
		if(!empty($update))
		{
			$return = 	array(
							'message' => 'Success Save Stage'
						);
			$code = 200;
		}
		else
		{
			$return = 	array(
							'message' => 'Fail Save Stage'
						);
			$code = 404;
		}
		
        return API::createResponse($return, $code);
    }
	
	public function getStage($app){
        $app_id 			= $app->getKey();
		$customer_ref_id	= Input::get('customer_ref_id');
		
        if ( ! $customer_ref_id)
        {
            return API::createResponse('Error, Cannot apply Cart (customer_ref_id is required).', 400);
        }
		
		$cart_stage = Cart::where('app_id', '=', $app_id)->where('customer_ref_id', '=', $customer_ref_id)->orderby('id', 'desc')->first();
        
		if(!empty($cart_stage->stage))
		{
			$return = 	array(
							'data' => array('stage' => $cart_stage->stage)
						);
			$code = 200;
		}
		else
		{
			$return = 	array(
							'message' => 'Fail Save Stage'
						);
			$code = 404;
		}
		
        return API::createResponse($return, $code);
    }
	
	
	/**
	 * This method is update cart field.
	 * validate required only
	 * @api
	 *
	 *	@params 
	 *		customer_ref_id - required,
	 *		customer_type - required,
	 *		field - required
	 *				ค่าที่ใช้ได้
	 *				bill_name 
	 *				bill_address
	 *				bill_province_id
	 *				bill_city_id
	 *				bill_district_id
	 *				bill_postcode
	 *				save_ccw
	 *				is_new_ccw
	 *						  
	 *		value - required,
	 *		
	 *		
	 *		
	 * @return response
	 */
	public function postSaveCartInfo($app){
        $app_id 			= $app->getKey();
		$customer_ref_id	= Input::get('customer_ref_id');
		$customer_type	= Input::get('customer_type');
		
		$field	= Input::get('field');
		$value	= Input::get('value');
		
		$cannot_use = array('app_id', 'customer_ref_id', 'customer_type');
		
		if ( ! $customer_ref_id)
        {
            return API::createResponse('Error, Cannot apply Cart (customer_ref_id is required).', 400);
        }
		
		if ( ! $field)
        {
            return API::createResponse('Error, Cannot apply Cart (field is required).', 400);
        }
		
		if ( ! $value)
        {
            return API::createResponse('Error, Cannot apply Cart (value is required).', 400);
        }
	
		if (in_array($field, $cannot_use)) 
		{
			return API::createResponse('Error, Cannot use field('.$field.').', 400);
		}
		
		try
		{
			$params = 	array(
						$field => $value
					);
			
			$validator = Validator::make(
				$params
				,
				array(
					$field => 'required'
				)
			);
			
			if ($validator->fails())
			{
				// The given data did not pass validation
				$messages = $validator->messages();
				return API::createResponse($messages, 400);
			}
			
			$update_status = Cart::where('app_id', '=', $app_id)->where('customer_ref_id', '=', $customer_ref_id)->where('customer_type', '=', $customer_type)->update($params);
			
			if(!empty($update_status))
			{
				if($update_status == 1)
				{
					$data = Cart::where('customer_ref_id', '=', $customer_ref_id)->where('app_id', '=', $app_id)->where('customer_type', '=', $customer_type)->get();
					
					$return = 	array(
									'message' => 'Update Cart success.',
									'data' => $data->toArray()
								);
					return API::createResponse($return, 200);
				}
				else
				{
					$return = 	array(
									'message' => 'Update Cart fail.'
								);
					return API::createResponse($return, 404);
				}
			}
			else
			{
				$return = 	array(
									'message' => 'Update Cart fail.'
								);
				return API::createResponse($return, 404);
			}
		}
		catch(Exception $e)
		{
			return API::createResponse('Error, Cannot apply Cart '.$e->getMessage().'.', 404);
		}
    }
}