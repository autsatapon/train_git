<?php

class PolicyAssignmentsController extends AdminController {

    protected $policyPerModel = null;

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Policy Assignment', URL::to('policies/assigns'));

        $this->policyPerModel = Config::get('global.policy_per_model', 3);
    }

    public function getIndex()
    {
        if (Input::get('shop'))
        {
            $shopQuery = Shop::query();
            $with = array();
            if (Input::get('vendor'))
            {
                $with['vendors'] = function($query)
                {
                    return $query->whereVendorId(Input::get('vendor'));
                };
            }
            if (Input::get('brand'))
            {
                $with['vendors.variants.product.brand'] = function($query)
                {
                    return $query->whereId(Input::get('brand'));
                };
            }
            $shopQuery->with($with);

            $shopQuery->whereShopId(Input::get('shop'));
            $shops = $shopQuery->get();


            /*
             * Build search criteria for policy relate
             */
            $policyRelateCriteria = array();

            foreach($shops as $shop)
            {
                $policyRelateCriteria['shop'] = $shop->shop_id;
                $policyRelateCriteria['vendors'] = array();
                $policyRelateCriteria['brands'] = array();

                if (! $shop->vendors)
                {
                    continue;
                }

                foreach ($shop->vendors as $vendor) {

                    $policyRelateCriteria['vendors'][] = $vendor->vendor_id;

                    if (! $vendor->variants)
                    {
                        continue;
                    }

                    foreach ($vendor->variants as $variant) {
                        if (
                            ! $variant->product
                            || ! $variant->product->brand
                            || ! $variant->product->brand->id
                        ) {
                            continue;
                        }
                        $policyRelateCriteria['brands'][] = $variant->product->brand->id;
                    }
                }
            }

            /*
             * get policy relate
             */
            $policyRelates = PolicyRelate::where(function($query) use ($policyRelateCriteria) {
                $query->where(function($query) use ($policyRelateCriteria) {
                    $query->wherePoliciableType("Shop")->where('policiable_id', $policyRelateCriteria['shop']);
                });

                if (isset($policyRelateCriteria['vendors']) && count($policyRelateCriteria['vendors']))
                {
                    $query->orWhere(function($query) use ($policyRelateCriteria) {
                        $query->wherePoliciableType('VVendor')->whereIn('policiable_id', $policyRelateCriteria['vendors']);
                    });
                }

                if (isset($policyRelateCriteria['brands']) && count($policyRelateCriteria['brands']))
                {
                    $query->orWhere(function($query) use ($policyRelateCriteria) {
                        $query->wherePoliciableType('Brand')->whereIn('policiable_id', $policyRelateCriteria['brands']);
                    });
                }
            })->get();


            $funcGetPolicyRelates = function($type, $id) use ($policyRelates)
            {
                $filter = function($model) use ($type, $id) {
                    return (
                        $model->policiable_type == $type
                        && $model->policiable_id == $id
                        );
                };
                return $policyRelates->filter($filter)->values();
            };

            $records = array();
            $recordRow = 0;

            $brandUniqueCollector = array();

            // s($policyRelateCriteria);
            // s($policyRelates->toArray());

            foreach($shops as $shop)
            {
                // protect error
                if (! isset($records[$recordRow]))
                {
                    $records[$recordRow] = array(0 => null, 1 => array(), 2 => array(), 3 => array());
                }

                $records[$recordRow][0] = $shop->shop_id;
                $records[$recordRow][1] = array(
                    'id' => $shop->shop_id,
                    'type' => 'shop',
                    'name' => $shop->shop_id.' - '.$shop->name,
                    'policies' => $funcGetPolicyRelates('Shop', $shop->shop_id)
                    );

                if (! $shop->vendors)
                {
                    // go next row for next shop
                    $recordRow++;
                    continue;
                }

                $recordRow++;
                foreach ($shop->vendors as $vendor) {

                    $policies = $funcGetPolicyRelates('VVendor', $vendor->vendor_id);

                    if (! Input::get('vendor'))
                    {
                        if (Input::get('display_filter') == "assigned")
                        {
                            if (! $policies->count())
                            {
                                continue;
                            }
                        }
                        if (Input::get('display_filter') == "non_assign")
                        {
                            if ($policies->count())
                            {
                                continue;
                            }
                        }
                    }

                    // protect error
                    // this if will occur when we go to next vendor
                    if (! isset($records[$recordRow]))
                    {
                        $records[$recordRow] = array(0 => null, 1 => array(), 2 => array(), 3 => array());
                    }

                    $records[$recordRow][2] = array(
                        'id' => $vendor->vendor_id,
                        'type' => 'vendor',
                        'name' => $vendor->vendor_id.' - '.$vendor->name,
                        'policies' => $policies
                        );

                    if (! $vendor->variants || $vendor->variants->count() < 1)
                    {
                        // vendor don't have variant
                        // go to next row for next vendor
                        $recordRow++;
                        continue;
                    }

                    $recordRow++;
                    foreach ($vendor->variants as $variant) {
                        if (
                            ! $variant->product
                            || ! $variant->product->brand
                            || ! $variant->product->brand->id
                        ) {
                            // vendor don't have product or brand
                            // but it can has many product or brand
                            // so we will stay in same row for next one
                            continue;
                        }

                        if (in_array($variant->product->brand->id, $brandUniqueCollector))
                        {
                            continue;
                        }

                        $policies = $funcGetPolicyRelates('Brand', $variant->product->brand->id);

                        if (Input::get('vendor'))
                        {
                            if (Input::get('display_filter') == "assigned")
                            {
                                if (! $policies->count())
                                {
                                    continue;
                                }
                            }
                            if (Input::get('display_filter') == "non_assign")
                            {
                                if ($policies->count())
                                {
                                    continue;
                                }
                            }
                        }

                        // protect error
                        // this if will occur when we go to next brand
                        if (! isset($records[$recordRow]))
                        {
                            $records[$recordRow] = array(0 => null, 1 => array(), 2 => array(), 3 => array());
                        }

                        $brandUniqueCollector[] = $variant->product->brand->id;

                        $records[$recordRow][3] = array(
                            'id' => $variant->product->brand->id,
                            'type' => 'brand',
                            'name' => $variant->product->brand->id.' - '.$variant->product->brand->name,
                            'policies' => $policies
                            );

                        // we will go to next row for next vendor
                        $recordRow++;
                    }

                    // we will go to next row for next vendor
                    $recordRow++;
                }

                // we will go to next row for next shop
                $recordRow++;
            }

        }

        // list all policy
        $policies = Policy::orderBy('type', 'asc')->orderBy('created_at', 'desc')->get();

        $policyOptions = array(
            'no' => __('No')
            );

        $policyType = Config::get('global.policy_type');

        foreach ($policies as $policy)
        {
            $policyOptions[$policy->id] = array_get($policyType, $policy->type.'.th_TH').' / '.$policy->title;
        }



        // create shop select option
        $shopSelect = $this->generateSelectOption('shop');

        // user selected shop so create vendor select option
        $vendorSelect = array();
        if (Input::get('shop'))
        {
            $vendorSelect = $this->generateSelectOption('vendor', Input::get('shop'));
        }

        // user selected vendor so create brand select option
        $brandSelect = array();
        if (Input::get('vendor'))
        {
            $brandSelect = $this->generateSelectOption('brand', Input::get('vendor'));
        }


        $this->theme->asset()->container('footer')->usePath()->add('policy-assignment', 'admin/js/policy_assignment.js', 'jquery');

        $this->data = array(
            'shops' => $shopSelect,
            'vendors' => $vendorSelect,
            'brands' => $brandSelect,
            'policyOptions' => $policyOptions,
            'policyPerModel' => $this->policyPerModel
            );

        if (! empty($records))
        {
            $this->data['records'] = $records;
        }
        return $this->theme->of('policies.assignments.index', $this->data)->render();
    }

    public function getSelectOption($slug, $ownerId = null, $selected = null)
    {
        $lists = $this->generateSelectOption($slug, $ownerId);

        if ($lists === false)
        {
            return App::abort();
        }

        // if (count($lists) < 1)
        // {
        //     $lists = array("false" => "No result");
        // }

        foreach ($lists as $value => $text) {
            $checked = ($selected == $value) ? 'checked' : '';
            echo "<option value='{$value}' {$checked}>{$text}</option>";
        }
    }

    private function generateSelectOption($slug, $ownerId = null)
    {
        if (! in_array($slug, array('shop', 'vendor', 'brand')))
        {
            return false;
        }

        switch ($slug) {
            case 'shop':
                $lists = Shop::orderBy('name')->lists('name', 'shop_id');
                foreach ($lists as $index => $shop) {
                    $lists[$index] = "{$index} - {$shop}";
                }
                break;

            case 'vendor':
                $lists = VVendor::whereShopId($ownerId)->orderBy('name')->lists('name', 'vendor_id');
                foreach ($lists as $vendorId => $name) {
                    $lists[$vendorId] = "{$vendorId} - {$name}";
                }
                break;

            case 'brand':
                $variants = ProductVariant::with('product.brand')->whereVendorId($ownerId)->get();
                $products = $variants->fetch('product')->filter(function($item){ return ($item == true); })->values();
                $brands = $products->fetch('brand')->filter(function($item){ return ($item == true); })->values();
                $lists = $brands->sort(function($item1, $item2) { return strcmp($item1['name'], $item2['name']); })->lists('name', 'id');
                break;
        }

        return $lists;
    }

    public function postAjaxUpdate()
    {
        $id = Input::get('id');
        $type = Input::get('type');
        $value = Input::get('value');
        $policyRelateID = Input::get('policy_relate_id');

        if (in_array($type, array('shop', 'brand', 'vendor')))
        {
            if ($type == "shop")
            {
                $model = Shop::findOrFail($id);
            }
            if ($type == "vendor")
            {
                $model = VVendor::findOrFail($id);
            }
            if ($type == "brand")
            {
                $model = Brand::findOrFail($id);
            }

            $model->load('policies');

            $policyRelate = $model->policies->filter(function($model) use ($policyRelateID) {
                return ($model->id == $policyRelateID);
            })->first();

            $policyRelate = $policyRelate ?: new PolicyRelate;

            if ($value != "no" && $value != "custom")
            {
                $policy = Policy::findOrFail($value);

                $policyRelate->policy_id = $policy->id;
                $policyRelate->use_type = "yes";
                $model->policies()->save($policyRelate);
            }

            if ($value == "no")
            {
                $policyRelate->use_type = "no";
                $policyRelate->save();
            }

            // flush api product detail
            Cache::tags('product')->flush();

            return array("status" => "success", "policy_relate_id" => $policyRelate->id);
        }

        return array("status" => "fail", "message" => "Fail!");
    }
}