<?php

class ProductSetVariantController extends AdminController {

	public function getIndex()
	{
	}

	public function getEdit($productId)
	{
		$product = Product::with(array('variants', 'styleTypes'))->findOrFail($productId);
		$productStyleTypes = $product->styleTypes->toJson();
		$styleTypes = StyleType::with(array('translates' => function($query)
		{
			$locales = Config::get('locale');
			return $query->whereLocale(key($locales)); 
		}))->get()->toJson();

		$viewData = compact('product', 'productStyleTypes', 'styleTypes');
		return $this->theme->of('products.set-variant.create-edit', $viewData)->render();
	}

}