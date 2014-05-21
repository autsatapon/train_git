<?php

class ProductSetVariantStyleController extends AdminController {

	protected $product;

	public function __construct(ProductRepositoryInterface $product)
	{
		parent::__construct();

		$this->product = $product;

		$this->theme->breadcrumb()->add('Product', URL::to('products'));
	}

	public function getIndex()
	{
		$products = $this->product->executeFormSearch()
			->with('brand','variants')
			->orderBy('title')->get();

		$products->sortBy(function($product)
		{
			return $product->brand->name;
		});

		$this->theme->setTitle('List Product');
		$view_data = compact('products');

        return $this->theme->of('products.set-variant-style.index', $view_data)->render();
	}

	public function getEdit($id)
	{

	}

	public function postEdit($id)
	{

	}

}