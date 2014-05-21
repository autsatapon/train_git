<?php
class ApiCustomerAddressesController extends ApiBaseController { 
	public function __construct()
	{
		parent::__construct();
	}



	public function postCreate(PApp $papp)
	{
		$app_id 			= $papp->getKey();
		$customer_ref_id	= Input::get('customer_ref_id');

		$inputs = Input::only('name', 'customer_ref_id', 'address', 'district_id', 'city_id', 'province_id', 'postcode', 'phone', 'email');		
		
		 
        // remove null value
        $inputs = array_filter($inputs);

        $validator = Validator::make(
		    array(
		    	'app_id' => $app_id,
		        'customer_ref_id' => Input::get('customer_ref_id'),
		        'name' => Input::get('name'),
		        'address' => Input::get('address'),
		        'district_id' => Input::get('district_id'),
		        'city_id' => Input::get('city_id'),
		        'province_id' => Input::get('province_id'),
		        'postcode' => Input::get('postcode'),
		        'phone' => Input::get('phone'),
		        'email' => Input::get('email')
		    ),
		    array(
		        'app_id' => 'required',
		        'customer_ref_id' => 'required',
		        'name' => 'required',
		        'address' => 'required',
		        'district_id' => 'required',
		        'city_id' => 'required',
		        'province_id' => 'required',
		        'postcode' => 'required|numeric',
		        'phone' => 'required',
		        'email' => 'required|email'
		    )
		);

		if ($validator->fails())
		{
    		// The given data did not pass validation
    		$messages = $validator->messages();
    		return API::createResponse($messages, 400);
		}

		$objAddress = new CustomerAddress();



		$objAddress->app_id = $app_id;
		$objAddress->customer_ref_id = Input::get('customer_ref_id');
		$objAddress->name = Input::get('name');
		$objAddress->address = Input::get('address');
		$objAddress->district_id = Input::get('district_id');
		$objAddress->city_id = Input::get('city_id');
		$objAddress->province_id = Input::get('province_id');
		$objAddress->postcode = Input::get('postcode');
		$objAddress->phone = Input::get('phone');
		$objAddress->email = Input::get('email');

		
		if ($objAddress->save())
		{
			#return API::createResponse($inputs);
			$address_id = $objAddress->id;	
			$response['message'] = "Create successfully";
			$response['address_id'] = $address_id; 
			

			return API::createResponse($response, 200);
		}
		else
		{
			$response['message'] = "Cannot create address";
			return API::createResponse($response, 400);
		}
	}

	public function getAddress(PApp $papp)
	{	
		$validator = Validator::make(
			array(
				'customer_ref_id' => Input::get('customer_ref_id')
			),
			array(
				'customer_ref_id' => 'required'
			)
		);
		if ($validator->fails())
		{
    		// The given data did not pass validation
    		$messages = $validator->messages();
    		return API::createResponse($messages, 400);
		}

		$ssoId = Input::get('customer_ref_id');
		
		$address = DB::table('customer_addresses AS CA')
			->select(
				'CA.id as customer_addresses_id', 'CA.name AS customer_name',
				'CA.app_id', 'CA.customer_ref_id',
				'CA.email', 'CA.province_id', 'CA.city_id', 'CA.district_id',
				'CA.address', 'CA.postcode', 'CA.phone',
				'provinces.name as province_name', 
				'cities.name as city_name', 
				'districts.name as district_name'
			)
    		->leftJoin('order_shipments', function($join)
        	{
            	$join->on('CA.id', '=', 'order_shipments.id');
        	})    		
        	->join('provinces', function($join)
        	{
        		$join->on('provinces.id', '=', 'CA.province_id');
        	})
			->join('cities', function($join)
			{
				$join->on('cities.id', '=', 'CA.city_id');
			})
			->join('districts', function($join)
			{
				$join->on('districts.id', '=', 'CA.district_id');
			})
    		->where('CA.customer_ref_id', '=', $ssoId)
    		->where('CA.app_id', '=', $papp->getKey())
    		->orderBy('order_shipments.created_at', 'DESC')
    		->orderBy('CA.created_at', 'DESC')
    		->get();
		
		
		return API::createResponse($address);
		
	}

	public function postUpdate()
	{

	}

	public function postSaveShipAddress(PApp $papp)
	{
		$app_id 			= $papp->getKey();
		$address_id 		= Input::get('address_id');
		$customer_ref_id	= Input::get('customer_ref_id');
	}

	public function postDelete(PApp $papp)
	{
		$app_id 			= $papp->getKey();
		$address_id 		= Input::get('address_id');
		$customer_ref_id	= Input::get('customer_ref_id');

		$validator = Validator::make(
		    array(
		    	'app_id' => $app_id,
		    	'address_id' => Input::get('address_id'),
		        'customer_ref_id' => Input::get('customer_ref_id')		        
		    ),
		    array(
		        'app_id' => 'required',
		        'address_id' => 'required',
		        'customer_ref_id' => 'required'		        
		    )
		);

		if ($validator->fails())
		{
    		// The given data did not pass validation
    		$messages = $validator->messages();
    		return API::createResponse($messages, 400);
		}
		
		$customer_address 	= new CustomerAddress;
		
		$delete_status = false;
		
		if(!empty($address_id) && !empty($customer_ref_id) )
		{
			$delete_status = CustomerAddress::where('app_id', '=', $app_id )->where('id', '=', $address_id)->where('customer_ref_id', '=', $customer_ref_id)->delete();
		}
		
		if(!empty($delete_status))
		{
			if($delete_status == 1)
			{
				$return = 	array(
								'message' => 'Delete address success.'
							);
				return API::createResponse($return, 200);
			}
			else
			{
				$return = 	array(
								'message' => 'Delete address fail.'
							);
				return API::createResponse($return, 404);
			}
		}
		else
		{
			$return = 	array(
								'message' => 'Delete address fail.'
							);
			return API::createResponse($return, 404);
		}
	}
	
	public function postSaveBillAddress(PApp $papp)
	{
		$cart 	= new Cart;
		
		$app_id 			= $papp->getKey();
		$customer_ref_id	= Input::get('customer_ref_id');
		
		$params = 	array(
						'bill_name' 		=> Input::get('bill_name'),
						'bill_address' 		=> Input::get('bill_address'),
						'bill_province_id' 	=> Input::get('bill_province_id'),
						'bill_city_id' 		=> Input::get('bill_city_id'),
						'bill_district_id' 	=> Input::get('bill_district_id'),
						'bill_postcode' 	=> Input::get('bill_postcode')
					);
					
		$save_ccw = Input::get('save_ccw');
		if(!empty($save_ccw))
		{
			$params['save_ccw'] = $save_ccw;
		}
		
		$is_new_ccw = Input::get('is_new_ccw'); 
		if(!empty($is_new_ccw))
		{
			$params['is_new_ccw'] = $is_new_ccw;
		}
		
		$validator = Validator::make(
		    array(
		    	'app_id' => $app_id,
		        'customer_ref_id' 	=> $customer_ref_id,
		        'bill_name' 		=> $params['bill_name'],		        
		        'bill_address' 		=> $params['bill_address'],		        
		        'bill_province_id' 	=> $params['bill_province_id'],		        
		        'bill_city_id' 		=> $params['bill_city_id'],		        
		        'bill_district_id' 	=> $params['bill_district_id'],		        
		        'bill_postcode' 	=> $params['bill_postcode']		        
		    ),
		    array(
		        'app_id' => 'required',
		        'customer_ref_id' => 'required',	        
		        'bill_name' => 'required',	        
		        'bill_address' => 'required',	        
		        'bill_province_id' => 'required',	        
		        'bill_city_id' => 'required',	        
		        'bill_district_id' => 'required',	        
		        'bill_postcode' => 'required'    
		    )
		);
		
		if ($validator->fails())
		{
    		// The given data did not pass validation
    		$messages = $validator->messages();
    		return API::createResponse($messages, 400);
		}
		
		$update_status = false;
		
		$update_status = Cart::where('customer_ref_id', '=', $customer_ref_id)->where('app_id', '=', $app_id)->update($params);
		
		if(!empty($update_status))
		{
			if($update_status == 1)
			{
				$data = Cart::where('customer_ref_id', '=', $customer_ref_id)->where('app_id', '=', $app_id)->get();
				
				$return = 	array(
								'message' => 'Update address success.',
								'data' => $data->toArray()
							);
				return API::createResponse($return, 200);
			}
			else
			{
				$return = 	array(
								'message' => 'Update address fail.'
							);
				return API::createResponse($return, 404);
			}
		}
		else
		{
			$return = 	array(
								'message' => 'Update address fail.'
							);
			return API::createResponse($return, 404);
		}
	}
}