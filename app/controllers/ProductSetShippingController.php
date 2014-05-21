<?php

class ProductSetShippingController extends AdminController {

	protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Product Shipping', URL::to('products/set-shipping'));
        //$this->theme->breadcrumb()->add('Set Product Shipping', URL::to('products/set-shipping'));
    }

    /*
    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Set Product Shipping', URL::to('products/set-shipping'));
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
		$products->load('brand', 'variants' ,'mediaContents');

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

		$this->theme->setTitle('Set Product Shipping');

		$view = compact('products');

		return $this->theme->of('products.set-shipping.index', $view)->render();
    }

    public function getEdit($pid = 0)
    {
		$this->theme->breadcrumb()->add('Edit Product Shipping', URL::to('products/set-shipping'));
		$product = Product::with('brand','variants')->find($pid);
        // $products = Product::where('id', $pid)->with('brand','variants')->get();
		$this->theme->setTitle('Edit Product Shipping');

		$view = compact('product');

        return $this->theme->of('products.set-shipping.edit', $view)->render();
    }

    public function postEdit($pid = 0)
    {
		$dimension_width = Input::get('dimension_width');
		$dimension_length = Input::get('dimension_length');
		$dimension_height = Input::get('dimension_height');
		$dimension_unit = Input::get('dimension_unit');
		$weight = Input::get('weight');
		$fragility = Input::get('fragility');
		$allow_cod = Input::get('allow_cod', 0);
		$vid = Input::get('vid');

		$product = Product::findOrFail($pid);
		$product->allow_cod = $allow_cod ? 1 : 0;
		$product->save();

		$failed = false;
		$errors = array();

		foreach ($vid  as $id => $value)
		{
			$variant = ProductVariant::find($id);

			$variant->dimension_width = floatval($dimension_width[$id]);
			$variant->dimension_length = floatval($dimension_length[$id]);
			$variant->dimension_height = floatval($dimension_height[$id]);

			$variant->weight = floatval($weight[$id]) * $dimension_unit[$id] ;
			$variant->fragility = isset($fragility[$id]) ? 'yes'  : 'no' ;

			if ($variant->save() == false)
			{
				return Redirect::to('/products/set-shipping/edit/'.$pid)->withInput()->with('errors', $variant->errors());
			}
		}

		//sd('end');

		if ( ! $failed)
		{
			$success = 'Product Shipping has been modified.';

			return Redirect::to('/products/set-shipping/edit/'.$pid)->with('success', $success);
		}
		else
		{
			return Redirect::to('/products/set-shipping/edit/'.$pid)->withInput()->with('errors', $errors);
		}
    }

}