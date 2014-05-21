<?php

class PoliciesBrandsController extends AdminController {

    /**
     * New constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Vendor\'s Policy', URL::to('policies/brands'));
    }

    /**
     * List all brands and policies.
     *
     * @return string
     */
    public function getIndex()
    {
		return Redirect::to('policies/vendors');
    }

	public function getVendor($vendor_id = 0)
    {
		 $brands_data = Brand::with('files')->get();
		 $policies_data = Policy::with('files')->get();
		$vendor = VVendor::with('policies', 'variants.product', 'variants', 'variants.product.brand')->findOrFail($vendor_id);

		$vendor_policies = array();
		$vendor_brand_policies = array();

		foreach($vendor->policies as $policy)
		{
			if($policy->pivot->brand_id == false)
				array_push($vendor_policies, $policy->pivot);
			else
				array_push($vendor_brand_policies, $policy->pivot);
		}

		$brandIdArr = array();
		foreach($vendor->variants as $key=>$variant)
		{
			$brandId = @$variant->product->brand->id;
			if ($brandId && !in_array($brandId, $brandIdArr) )
			{
				$brandIdArr[] = $brandId;
			}else{
			}
		}

		if(!empty($brandIdArr)){
			$brands = Brand::whereIn('id', $brandIdArr)->get();
		}else{
			return Redirect::to('/policies/vendors/')->withErrors('This vendor not have product.');
		}

		/*
		foreach ($brands as $key => $brand) {
			// $brandPoliciesArr = $brand->policies->fetch('id')->toArray();
			d($brand->id, $brandPoliciesArr);
		}

		die();
	*/
		//sd($brands->toArray());
		//$brands = array_unique($brands);
		$policies = Policy::with('files')->get();

		$arr1 = DB::table('vendors_policies')->select('policy_id')->distinct()->where('vendor_id', $vendor->vendor_id)->where('brand_id', null)->lists('policy_id');

		$arr2 = array();
			foreach ($vendor_brand_policies as $key=>$val)
			{
				$arr2[$val->brand_id][$val->policy_id] = $val->toArray();
			}


		$arr3 = array();
			foreach ($vendor_policies as $key=>$val)
			{
				$arr3[$val->brand_id][$val->policy_id] = $val->toArray();
			}

        $data = compact('vendor_policies', 'vendor_brand_policies','policies_data','vendor','brands','arr1','arr2','arr3');

        $this->theme->setTitle('Brand\'s Policy');

        return $this->theme->of('policiesbrands.index', $data)->render();
    }

   public function getCreate($brand_id = 0, $policy_id = 0, $vendor_id = 0)
    {
    	$brand = Brand::findOrFail($brand_id);
		$policy = Policy::findOrFail($policy_id);
		$vendor = VVendor::findOrFail($vendor_id);
		$data = compact('brand', 'policy','vendor');

		$this->theme->breadcrumb()->add('Create Brand\'s Policy');
        $this->theme->setTitle('Create Policy');

        return $this->theme->of('policiesbrands.create', $data)->render();
    }

    public function postCreate($brand_id = 0, $policy_id = 0, $vendor_id = 0)
    {
		$policy_title = Input::get('policy_title');
        $policy_description = Input::get('policy_description');
		$status = Input::get('status');

		DB::table('vendors_policies')->insert(
		    array('vendor_id' => $vendor_id,'policy_id' => $policy_id,'brand_id' => $brand_id,'policy_title' => $policy_title, 'policy_description' => $policy_description ,'status' => $status)
		);

		$this->theme->setTitle('Brand\'s Policy');

        $success = 'Brand policy has been Saved.';

        return Redirect::to('/policies/brands/vendor/'.$vendor_id)->with('success', $success);
    }

    public function getEdit($vendor_policy_id = 0, $brand_id = 0, $policy_id = 0, $vendor_id = 0)
    {

		$brand = Brand::findOrFail($brand_id);
		$policy = Policy::findOrFail($policy_id);
		$vendor = VVendor::findOrFail($vendor_id);

		$vendor_policy = '';
		if($vendor_policy_id != 0)
		{
			$brand_policy_type = 'edit';
			foreach($vendor->policies as $policy)
			{
				if ($vendor_policy_id == $policy->pivot->id)
				{
					$vendor_policy = $policy->pivot->toArray();
					break;
				}

			}

			$data = compact('brand', 'policy' ,'vendor','vendor_policy');
		}

		$data = compact('brand', 'policy','vendor','vendor_policy');

		$this->theme->breadcrumb()->add('Edit Brand\'s Policy');
        $this->theme->setTitle('Edit Policy');

        return $this->theme->of('policiesbrands.edit', $data)->render();
    }

    public function postEdit($vendor_policy_id = 0, $brand_id = 0, $policy_id = 0, $vendor_id = 0)
    {

        $policy_title = Input::get('policy_title');
        $policy_description = Input::get('policy_description');
		$status = Input::get('status');

		DB::table('vendors_policies')
            ->where('id', $vendor_policy_id)
            ->update(array('policy_title' => $policy_title,'policy_description' => $policy_description,'status' => $status));

		/*if ( !$vendorData->save() )
        {
            return Redirect::to('policies/brands/edit/'.$vendor_policy_id.'/'.$brand_id.'/'.$policy_id.'/'.$vendor_id)->withInput()->withErrors($vendorData->errors());
        }*/

		$this->theme->setTitle('Brand\'s Policy');

        $success = 'Brand policy has been modified.';

        return Redirect::to('/policies/brands/vendor/'.$vendor_id)->with('success', $success);
    }
	/*
	public function getDelete($brand_id = 0, $policy_id = 0)
    {
		$brand = Brand::findOrFail($brand_id);

		$brand->policies()->detach($policy_id);

        $success = 'Brand policy has been deleted.';

        return Redirect::to('/policies/brands')->with('success', $success);
    }
	*/
}