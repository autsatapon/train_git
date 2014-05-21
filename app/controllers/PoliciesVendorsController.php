<?php

class PoliciesVendorsController extends AdminController {

    /**
     * New constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Vendor\'s Policy', URL::to('policies/vendors'));
    }

    public function getIndex()
    {
    	$vendors = VVendor::has('policies', '>', '0')->get();
		$vendors->load('policies');
		$policies = Policy::with('files')->get();
		$data = compact('vendors', 'policies');
		$this->theme->setTitle('Vendor\'s Policy');

        return $this->theme->of('policies.policiesvendors.index', $data)->render();
    }

	public function getCreate()
    {
		$vendors = VVendor::has('policies', '=', '0')->orderby('name')->get();
		$policies = Policy::with('files')->get();

		$data = array();
		$data['vendors'] = $vendors;
		$data['policies'] = $policies;

		$this->theme->setTitle('Create Vendor\'s Policy');

        return $this->theme->of('policies.policiesvendors.create', $data)->render();
    }

	public function postCreate()
    {
		$vendor_id = Input::get('vendor_id');
		$policyTitleArr = Input::get('title');
		$policyDescriptionArr = Input::get('description');
        $policyIdArr = Input::get('policy_type');

		$policyIdArr = array_filter($policyIdArr, function($val){
			return($val != 0);
		});

		$vendorPolicyData = array();
		foreach($policyIdArr as $policyId)
		{
			$vendorPolicyData[$policyId] = array(
				'policy_title' => $policyTitleArr[$policyId],
				'policy_description' => $policyDescriptionArr[$policyId]
			);
		}

		$vendor = VVendor::find($vendor_id);
		// $vendor->policies()->sync($policyIdArr);
		$vendor->policies()->sync($vendorPolicyData);

		return Redirect::to('policies/vendors');
	}

    public function getEdit($vendor_id = 0)
    {

		$vendors = VVendor::with('policies')->findOrFail($vendor_id);
		$policies = Policy::with('files')->get();

		$this->data['vendors'] = $vendors;
		$this->data['policies'] = $policies;

        $data = compact('vendors', 'policies');

		$this->theme->setTitle('Edit Vendor\'s Policy');
		$success = 'Vendor policy has been Added.';

		 return $this->theme->of('policies.policiesvendors.edit', $data)->render();
    }

    public function postEdit($vendor_id = 0, $policy_id = 0)
    {
		$vendors = VVendor::with('policies')->findOrFail($vendor_id);

        // $policies = Policy::with('files')->get();

        $vendor_id = Input::get('vendor_id');
        $policyTitleArr = Input::get('title');
        $policyDescriptionArr = Input::get('description');
        $policyIdArr = Input::get('policy_type');

        $policyIdArr = array_filter($policyIdArr, function($val){
            return($val != 0);
        });

        $vendorPolicyData = array();
        foreach($policyIdArr as $policyId)
        {
            $vendorPolicyData[$policyId] = array(
                'policy_title' => $policyTitleArr[$policyId],
                'policy_description' => $policyDescriptionArr[$policyId]
            );
        }

        // $vendor->policies()->sync($policyIdArr);
        $vendor->policies()->sync($vendorPolicyData);

        return Redirect::to('policies/vendors');
    }

	public function getDelete($vendor_id = 0, $policy_id = 0)
    {
		$vendor = VVendor::findOrFail($vendor_id);

		$vendor->policies()->detach($policy_id);

        $success = 'Vendor policy has been deleted.';

        return Redirect::to('/policies/vendors')->with('success', $success);
    }

}