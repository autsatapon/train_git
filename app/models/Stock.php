<?php

class Stock extends PCMSModel {

	public static function isNonStock($stockCode)
	{
		return ($stockCode == 4 || $stockCode == 6);
	}
	public static function isStock($stockCode)
	{
		return ! static::isNonStock($stockCode);
	}

	public static function getStockType($stockCode)
	{
		return static::isNonStock($stockCode) ? 'non-stock' : 'stock';
	}

    public function shippingMethods()
    {
        return $this->hasMany('StockShippingMethod', 'stock_type');
    }

}