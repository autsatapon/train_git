<?php

class ShippingMethodRepository implements ShippingMethodRepositoryInterface {

	public function getShippingMethodInstance($object)
	{
		if ($object instanceof Stock)
		{
			$className = 'StockShippingMethod';
		}
		else if ($object instanceof VVendor)
		{
			$className = 'VendorShippingMethod';
		}
		else if ($object instanceof Product)
		{
			$className = 'ProductShippingMethod';
		}
		else
		{
			throw new Exception('Object must be instanceof Stock, VVendor or Product. Instanceof '.get_class($object).' has been sent.');
		}

		return new $className;
	}

	public function getOptions()
	{
		return array(
			'0' => 'Inherit from Vendor',
			'1|2|3' => 'RGP, EMS, D2D',
			'2|3' => 'EMS, D2D',
			'3' => 'D2D',
			'4' => 'D2D Express'
		);
	}

	public function getValue($model)
	{
		$shipping_method_ids = $model->shippingMethods()->orderBy('shipping_method_id', 'ASC')->lists('shipping_method_id');

		return implode('|', $shipping_method_ids);
	}

	public function setShippingMethod($model, $values)
	{
		$model->shippingMethods()->delete();

		if ($values != 0)
		{
			foreach (explode('|', $values) as $value)
			{
				$shippingMethod = $this->getShippingMethodInstance($model);
				$shippingMethod->shipping_method_id = $value;

				$model->shippingMethods()->save($shippingMethod);
			}
		}
	}

	public function getByProduct(Product $product)
	{
		$productShippingMethods = $product->shippingMethods;

		if ( !$productShippingMethods->isEmpty() )
		{
			$methodListArr = $productShippingMethods->lists('shipping_method_id');

			return ShippingMethod::whereIn('id', $methodListArr)->get();
		}

		return NULL;
	}

	public function getByVendor(VVendor $vendor)
	{
		$vendorShippingMethods = $vendor->shippingMethods;

		if ( !$vendorShippingMethods->isEmpty() )
		{
			$methodListArr = $vendorShippingMethods->lists('shipping_method_id');

			return ShippingMethod::whereIn('id', $methodListArr)->get();
		}

		return NULL;
	}

	public function getByStockType($stockType)
	{
		$methodListArr = StockShippingMethod::where('stock_type', $stockType)->lists('shipping_method_id');

		if (!empty($methodListArr))
		{
			return ShippingMethod::whereIn('id', $methodListArr)->get();
		}

		return NULL;
	}

}