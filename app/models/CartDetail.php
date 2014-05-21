<?php

class CartDetail extends Harvey {

	protected $table = 'cart_details';
	protected $softDelete = true;
	protected $with = array('variant');

    public function cart()
    {
        return $this->belongsTo('Cart');
    }

	public function product()
	{
		return $this->belongsTo('Product');
	}

	public function vvendor()
	{
		return $this->belongsTo('VVendor', 'vendor_id');
	}

	public function shop()
	{
		return $this->belongsTo('Shop', 'shop_id', 'shop_id');
	}

	public function variant()
	{
		return $this->belongsTo('ProductVariant');
	}

    public function getTotalPriceAttribute()
    {
    	return $this->attributes['price'] * $this->attributes['quantity'] ;
    }

    // public function getProductTitleAttribute()
    // {
    //     $styleOptionText = array();

    //     $styleOptionId = $this->variant->styleOptions->lists('id');

    //     $this->product->styleOptions->each(function($styleOption) use(&$styleOptionText, $styleOptionId)
    //     {
    //         if (in_array($styleOption->id, $styleOptionId))
    //         {
    //             $styleOptionText[] = $styleOption->pivot->text;
    //         }
    //     });

    //     return $this->product->title.' ('.implode(', ', $styleOptionText).')';
    // }

    public function getImageAttribute()
    {
        $image = null;

        if ($this->variant)
        {
            $image = $this->variant->image;
        }

        if (! $image)
        {
            $image = $this->product->image;
        }

        return $image;
    }

}