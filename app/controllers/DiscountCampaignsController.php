<?php

class DiscountCampaignsController extends AdminController {

    private $type = array(
        //'today_special' => 'Today Special',
        'flash_sale' => 'Flash Sale',
        'itruemart_tv' => 'iTrueMart TV',
        // 'on_sale' => 'On Sale'
    );
    private $discount = array(
        'percent' => '%',
        'price' => 'บาท'
    );
//    private $status = array(
//        'draft' => 'Draft',
//        'published' => 'Published'
//    );

    private $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Discount campaigns', URL::to('discount-campaigns'));

        $this->theme->setTitle('Discount Campaigns');
    }

    public function getIndex()
    {
        $discountCampaigns = DiscountCampaign::with(array('pApp'))->where('type', '!=', 'on_sale')->orderBy('created_at', 'desc')->get();

        $view['typeOptions'] = $this->type;
        $view['discountCampaigns'] = $discountCampaigns;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('discount-campaigns-index', 'admin/js/discount-campaigns-index.js', array('jquery', 'jqueryui'));

        return $this->theme->of('discount-campaigns.index', $view)->render();
    }

    public function getCreate()
    {
        $this->theme->breadcrumb()->add('Create');

        $appOptions = PApp::all()->lists('name', 'id');

        $view['appOptions'] = $appOptions;
        $view['typeOptions'] = $this->type;
        $view['discountOptions'] = $this->discount;
//        $view['statusOptions'] = $this->status;

        $this->theme->asset()->container('footer')->usePath()->add('discount-campaigns-create', 'admin/js/discount-campaigns-create.js', array('jquery', 'jqueryui'));

        return $this->theme->of('discount-campaigns.create', $view)->render();
    }

    public function postCreate()
    {
        $discountCampaign = new DiscountCampaign;

        $discountCampaign->app_id = Input::get('app_id');
        $discountCampaign->type = Input::get('type');
        $discountCampaign->code = Input::get('code');
        $discountCampaign->name = Input::get('name');
        $discountCampaign->description = Input::get('description');
        $discountCampaign->note = Input::get('note');
        $discountCampaign->discount = Input::get('discount');
        $discountCampaign->discount_type = Input::get('discount_type');
        $discountCampaign->started_at = Input::get('started_at').' 00:00:00';
        $discountCampaign->ended_at = Input::get('ended_at').' 23:59:59';
//        $discountCampaign->status = Input::get('status');

        if (!$discountCampaign->save())
        {
            $errors = $discountCampaign->errors();

            return Redirect::back()->withErrors($errors)->withInput();
        }

        return Redirect::to('discount-campaigns')->with('success', 'Discount campaign created');
    }

    public function getEdit($id)
    {
        $this->theme->breadcrumb()->add('Edit');

        $discountCampaign = DiscountCampaign::findOrFail($id);

        $appOptions = PApp::all()->lists('name', 'id');

        $view['discountCampaign'] = $discountCampaign;
        $view['appOptions'] = $appOptions;
        $view['typeOptions'] = $this->type;
        $view['discountOptions'] = $this->discount;
//        $view['statusOptions'] = $this->status;

        $this->theme->asset()->container('footer')->usePath()->add('discount-campaigns-create', 'admin/js/discount-campaigns-create.js', array('jquery', 'jqueryui'));

        return $this->theme->of('discount-campaigns.edit', $view)->render();
    }

    public function postEdit($id)
    {
        $discountCampaign = DiscountCampaign::with(array('specialDiscounts.productVariant.product'))->findOrFail($id);

        // $discountCampaign->app_id = Input::get('app_id');
        // $discountCampaign->type = Input::get('type');
        $discountCampaign->code = Input::get('code');
        $discountCampaign->name = Input::get('name');
        $discountCampaign->description = Input::get('description');
        $discountCampaign->note = Input::get('note');
        $discountCampaign->discount = Input::get('discount');
        $discountCampaign->discount_type = Input::get('discount_type');
        $discountCampaign->started_at = Input::get('started_at').' 00:00:00';
        $discountCampaign->ended_at = Input::get('ended_at').' 23:59:59';
//        $discountCampaign->status = Input::get('status');

        if (!$discountCampaign->save())
        {
            $errors = $discountCampaign->errors();

            return Redirect::back()->withErrors($errors)->withInput();
        }

        if ( !$discountCampaign->specialDiscounts->isEmpty() )
        {
            // get Object products
            $products = array();

            $discountCampaign->specialDiscounts->each(function($item) use (&$products, $discountCampaign)
            {
                $products[$item->productVariant->product->getKey()] = $item->productVariant->product;

                $item->started_at = $discountCampaign->started_at;
                $item->ended_at = $discountCampaign->ended_at;
                $item->save();
            });

            // update elastic
            foreach ($products as $p)
            {
                ElasticUtils::updateProduct($p);
            }
        }



        return Redirect::to('discount-campaigns')->with('success', 'Discount campaign edited');
    }

    public function getDelete($id)
    {
        $specialDiscounts = SpecialDiscount::where('discount_campaign_id', $id)->get();

        if ( !$specialDiscounts->isEmpty() )
        {
            $products = array();
            $specialDiscounts->each(function($item) use (&$products) {
                $products[$item->productVariant->product->getKey()] = $item->productVariant->product;
            });
        }

        SpecialDiscount::where('discount_campaign_id', $id)->delete();

        DiscountCampaign::findOrFail($id)->delete();

        if (!empty($products))
        {
            // update elastic
            foreach ($products as $p)
            {
                ElasticUtils::updateProduct($p);
            }
        }

        return Redirect::to('discount-campaigns')->with('success', 'Discount campaign deleted');
    }

    public function getList($id, $addItems = null)
    {
        $this->theme->breadcrumb()->add('List');

        $discountCampaign = DiscountCampaign::with(array('specialDiscounts.productVariant.product'))->findOrFail($id);

        $products = array();
        $variants = array();

        foreach ($discountCampaign->specialDiscounts as $discount)
        {
            $variant = $discount->productVariant;
            if (empty($variant))
            {
                continue;
            }

            $productId = $variant->product_id;

            if (!array_key_exists($productId, $products))
            {
                $products[$productId] = $discount->productVariant->product->toArray();
                $products[$productId]['variants'] = array();
                $products[$productId]['image'] = $discount->productVariant->product->image;
            }

            $products[$productId]['variants'][] = $discount->toArray();
            $variants[] = $discount->variant_id;
        }

        $newProducts = array();

        // ini_set('display_errors', 'on');

        if (Input::has('added-products'))
        {
            if ($variants != false)
                $withVariants = array('variants' => function($q) use ($variants)
                    {
                        $q->whereNotIn('id', $variants);
                    });
            else
                $withVariants = array('variants');

            $newProducts = Product::with($withVariants)
                    ->whereIn('id', Input::get('added-products'))->get();
        }

        $view['discountCampaign'] = $discountCampaign;
        $view['products'] = $products;
        $view['newProducts'] = $newProducts;

        $view['discountOptions'] = $this->discount;

        $this->theme->asset()->container('footer')->usePath()->add('discount-campaigns-list', 'admin/js/discount-campaigns-list.js', array('jquery', 'jqueryui'));

        return $this->theme->of('discount-campaigns.list', $view)->render();
    }

    public function postList($id)
    {
        $discountCampaign = DiscountCampaign::with(array('specialDiscounts'))->findOrFail($id);

        $productIds = array();

        $added = 0;
        $deleted = 0;

        $rebuildElastic = function($productIds)
        {
            if (!empty($productIds))
            {
                $productIds = array_unique($productIds);

                foreach ($productIds as $productId)
                {
                    if (empty($productId))
                        continue;
                    $product = Product::find($productId);

                    if (empty($product))
                        continue;

                    $product->touch();
                    // ElasticUtils::updateProduct($product);
                }
            }
        };

        // add new variants
        foreach (Input::get('added-variants', array()) as $key => $vatiant)
        {
            if (is_null($discountCampaign->specialDiscounts()->whereVariantId($key)->first()))
            {
                $added++;

                // if ($vatiant['discount_type'] == 'percent')
                // {
                //     $vatiant['discount'] = ($vatiant['discount'] <= 100 && $vatiant['discount'] >= 0)?$vatiant['discount']:100;
                //     $discount_price = (100-$vatiant['discount'])/100*$vatiant['net_price'];
                // }
                // else
                // {
                //    // $discount_price = ($vatiant['discount'] <= $vatiant['net_price'] && $vatiant['discount'] >= 0)?:$vatiant['net_price'];
                //     $discount_price = $vatiant['net_price']-(($vatiant['discount'] > $vatiant['net_price'] || $vatiant['discount'] < 0)?0:$vatiant['discount']);
                // }

                $specialDiscount = new SpecialDiscount;

                $specialDiscount->app_id = $discountCampaign->app_id;
                $specialDiscount->campaign_type = $discountCampaign->type;
                $specialDiscount->variant_id = $key;
                $specialDiscount->inventory_id = $vatiant['inventory_id'];
                // $specialDiscount->discount_price = $discount_price;

                $specialDiscount->discount = $vatiant['discount'];
                $specialDiscount->discount_type = $vatiant['discount_type'];
                $specialDiscount->buildDiscountPrice($vatiant['net_price']);
                $specialDiscount->started_at = $vatiant['started_at'].' 00:00:00';
                $specialDiscount->ended_at = $vatiant['ended_at'].' 23:59:59';

                // $discountCampaign->specialDiscounts()->save($specialDiscount);

                if (! $discountCampaign->specialDiscounts()->save($specialDiscount))
                {
                    $rebuildElastic($productIds);

                    $errors = $specialDiscount->errors();

                    return Redirect::back()->withErrors($errors)->withInput();
                }
            }

            $variantId = $key;
            $productIds[] = ProductVariant::where('id', $variantId)->pluck('product_id');
        }

        // edit old variants
        foreach (Input::get('variants', array()) as $key => $vatiant)
        {
            $specialDiscount = SpecialDiscount::findOrFail($key);

            if (isset($vatiant['delete']))
            {
                $deleted++;
                $specialDiscount->delete();
            }
            else
            {
                // if ($vatiant['discount_type'] == 'percent')
                // {
                //     $vatiant['discount'] = ($vatiant['discount'] <= 100 && $vatiant['discount'] >= 0)?$vatiant['discount']:100;
                //     $discount_price = (100-$vatiant['discount'])/100*$vatiant['net_price'];
                // }
                // else
                // {
                //    // $discount_price = ($vatiant['discount'] <= $vatiant['net_price'] && $vatiant['discount'] >= 0)?:$vatiant['net_price'];
                //     $discount_price = $vatiant['net_price']-(($vatiant['discount'] > $vatiant['net_price'] || $vatiant['discount'] < 0)?0:$vatiant['discount']);
                // }
                // $specialDiscount->discount_price = $discount_price;
                $specialDiscount->discount = $vatiant['discount'];
                $specialDiscount->discount_type = $vatiant['discount_type'];
                $specialDiscount->buildDiscountPrice($vatiant['net_price']);
                $specialDiscount->started_at = $vatiant['started_at'].' 00:00:00';
                $specialDiscount->ended_at = $vatiant['ended_at'].' 23:59:59';

                // $specialDiscount->save();

                if (! $specialDiscount->save())
                {
                    $rebuildElastic($productIds);

                    $errors = $specialDiscount->errors();

                    return Redirect::back()->withErrors($errors)->withInput();
                }
            }

            $variantId = $specialDiscount->variant_id;
            $productIds[] = ProductVariant::where('id', $variantId)->pluck('product_id');
        }

        // rebuild elastic
        $rebuildElastic($productIds);

        $message = 'Updated successful';

        if ($added)
        {
            $message .= ', '.$added.' '.Str::plural('item', $added).' added';
        }

        if ($deleted)
        {
            $message .= ', '.$deleted.' '.Str::plural('item', $deleted).' deleted';
        }

        return Redirect::to('discount-campaigns/list/'.$id)->with('success', $message);
    }

    public function getAddItems($id)
    {
        $this->theme->breadcrumb()->add('Add items');

        $parseUrl = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parseUrl['query']))
        {
            $discountCampaign = DiscountCampaign::with(array('specialDiscounts.productVariant'))->findOrFail($id);

            $existsProducts = array();

            foreach ($discountCampaign->specialDiscounts as $discount)
            {
                $existsProducts[] = $discount->productVariant->product->getKey();
            }

            $existsProducts = array_unique($existsProducts);

            $productRepository = new ProductRepository();

//            if (empty($existsProducts))
//            {
//                $productRepository = new ProductRepository();
//            }
//            else
//            {
//                $productRepository = new ProductRepository(Product::whereNotIn('id', $existsProducts));
//            }

            $products = $productRepository->getExecuteFormSearch();
            $products->load('brand', 'variants', 'revisions', 'collections');
        }
        else
        {
            $products = array();
        }

        $view['products'] = $products;
        $view['campaignId'] = $id;

        $this->theme->asset()->container('footer')->usePath()->add('discount-campaigns-add-items', 'admin/js/discount-campaigns-add-items.js', 'jquery');

        return $this->theme->of('discount-campaigns.add-items', $view)->render();
    }

}

