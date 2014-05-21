<?php

class ProductSetPriceController extends AdminController {

    protected $product;

    protected $installmentPeriods = array(3, 6, 10);

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Product Price', URL::to('products/set-price'));
        //$this->theme->breadcrumb()->add('Set Product Price', URL::to('products/set-price'));
    }

    /*
    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Set Product Price', URL::to('products/set-price'));
    }
    */

    public function getIndex()
    {
        /*
        $products = new ProductRepository();
        $products = $products->executeFormSearch()->with('brand','variants')->get();
        */
        $products = $this->product->executeFormSearch()->orderBy('title')->get();

        $products = $this->product->filterSearchResults($products);
        $products->load('brand', 'variants');

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

        $this->theme->setTitle('Set Product Price');

        $view = compact('products');

        return $this->theme->of('products.set-price.index', $view)->render();
    }

    public function getEdit($pid = 0)
    {
        $this->theme->breadcrumb()->add('Edit Product Price', URL::to('products/set-price'));
        $product = Product::with('brand','variants')->find($pid);
        // $products = Product::where('id', $pid)->with('brand','variants')->get();
        $this->theme->setTitle('Edit Product Price');

        $user = Sentry::getUser();
        $productRevisionsOfUser = $product->revisions()->whereIn('status', array('draft', 'rejected', 'approved'))->where('editor_id', $user->id)->get();

        $view = compact('product');
        $view['revisions'] = $productRevisionsOfUser;
        $view['installmentPeriods'] = $this->installmentPeriods;

        return $this->theme->of('products.set-price.edit', $view)->render();
    }

    public function postEdit($pid = 0)
    {
        $product = Product::findOrFail($pid);

        $old_prices = Input::get('old_price');
        $special_prices = Input::get('special_price');
        $free_items = Input::get('free_item');

        $allow_installment = Input::get('allow-installment-product')==='yes' ? true : false;
        $installment_periods = Input::get('installment-product');

        $failed = false;
        $errors = array();
        $variantPricesArr = array();

        if ($allow_installment && count($installment_periods)>0)
        {
            $installments = array(
                'allow' => true,
                'periods' => array_values($installment_periods),
            );
        }
        else
        {
            $installments = array(
                'allow' => false
            );
        }

        $product->installment = json_encode($installments);

        // d(Input::all()); die();
        foreach ($old_prices as $id => $value)
        {
            $variant = ProductVariant::find($id);

            $allow_installment_variant = Input::get("allow-installment-variant.$id", null);
            $installment_periods_variant = Input::get("installment-variant.$id");

            if ($allow_installment_variant === 'yes' && count($installment_periods_variant))
            {
                $variant->installment = json_encode(array(
                    'allow' => true,
                    'periods' => array_values($installment_periods_variant)
                ));
            }
            else if ($allow_installment_variant === 'no')
            {
                $variant->installment = json_encode(array(
                    'allow' => false,
                ));
            }
            else
            {
                $variant->installment = null;
            }
            $variant->save();


            // Set Price
            $old_price = floatval($old_prices[$id]);
            $special_price = floatval($special_prices[$id]);

            if (isset($free_items[$id]))
            {
                $variant->free_item = 'yes';

                // เดิมมันทำใน function boot ของ model ProductVariant
                // แต่ถ้าจะทำให้ save draft ได้ ต้องยกโค้ดส่วนนี้มาทำตรงนี้ด้วยเท่านั้น !!
                $variant->normal_price = $variant->normal_price > 0 ? $variant->normal_price : $variant->price;
                $variant->price = 0;
            }
            else
            {
                $variant->free_item = 'no';
                if ($special_price == 0)
                {
                    $variant->price = $old_price;
                    $variant->normal_price = 0;
                }
                elseif ($old_price > 0)
                {
                    $variant->price = $special_price;
                    $variant->normal_price = $old_price;
                }
            }

            // Validate Before Save Draft.
            if ($variant->normal_price > 0)
            {
                $variant->addValidate(
                    array('normal_price' => $variant->normal_price),
                    array('normal_price' => 'required|numeric|min:'.$variant->price)
                );
            }

            if ($variant->validate(ProductVariant::$rules) == false)
            {
                return Redirect::to('/products/set-price/edit/'.$pid)->withInput()->with('errors', $variant->errors());
            }

            // Validate Passed, Save Draft Price. (Set to array before save draft)
            $variantDirty = $variant->getDirty();
            if (!empty($variantDirty))
            {
                $variantPricesArr["{$variant->id}"] = array(
                    'variant_id' => $variant->id,
                    'price' => $variant->price,
                    'normal_price' => $variant->normal_price,
                    'free_item' => $variant->free_item
                );
            }
/*
            if ($variant->save() == false)
            {
                return Redirect::to('/products/set-price/edit/'.$pid)->withInput()->with('errors', $variant->errors());
            }
*/
        }

        $this->product->saveDraft($pid, array('price' => $variantPricesArr));
        $product->save();

        //sd('end');

        if ( ! $failed)
        {
            $success = 'Product Price has been modified.';

            return Redirect::to('/products/set-price/edit/'.$pid)->with('success', $success);
        }
        else
        {
            return Redirect::to('/products/set-price/edit/'.$pid)->withInput()->with('errors', $errors);
        }
    }

}