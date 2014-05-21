<?php

class VVendor extends PCMSModel {

	//protected $table = 'view_vendors';
	protected $table = 'vendors';
	protected $primaryKey = 'vendor_id';
	protected $fillable = array('vendor_id', 'shop_id', 'master_id', 'name', 'stock_type');
	public $timestamps = false;

	protected $appends = array('vendor_detail');

	public static $autoKey = false;
	protected $softDelete = false;

	public static $labels = array(
		'name' => 'Vendor Name',
	);

	// public function policies()
	// {
	// 	return $this->belongsToMany('Policy', 'vendors_policies', 'vendor_id', 'policy_id')->withPivot('id','brand_id','status', 'policy_title', 'policy_description');
	// }

    public function shop()
    {
        return $this->belongsTo('Shop');
    }

	public function policies()
    {
        return $this->morphMany('PolicyRelate', 'policiable');
    }

	public function variants()
	{
		return $this->hasMany('ProductVariant', 'vendor_id');
	}

    public function shippingMethods()
    {
        return $this->hasMany('VendorShippingMethod', 'vendor_id');
    }

    public function methods()
    {
        return $this->belongsToMany('ShippingMethod', 'vendor_shipping_methods', 'vendor_id', 'shipping_method_id');
    }

	public function stock()
	{
		return $this->belongsTo('Stock', 'stock_type', 'stock_type');
	}

	public function getVendorAttribute()
	{
		return $this->name;
	}

	public function setVendorAttribute($vendor)
	{
		$this->attributes['name'] = $vendor;
	}

	public function getVendorDetailAttribute()
	{
		return $this->name.' | '.($this->master_id ?: $this->shop_id);
	}

}