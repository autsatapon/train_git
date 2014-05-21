<?php

class ImportedMaterial extends Harvey {

	// protected $guarded = array('id','product_id', 'vat', 'full_name', 'sku_true','create_at','update_at','price','cost_rtp');
	protected $guarded = array('id');

	public static $rules = array(
		'inventory_id' => 'required',
		'onCreate' => array(
			'inventory_id' => 'required|unique:imported_materials,inventory_id',
		),
	);

	public function getVendorDetailAttribute()
	{
		return $this->vendor_name.' ( '.($this->master_id ?: $this->shop_id).' )';
	}

	public function scopeNewMaterial($query)
	{
		return $query->whereNull('variant_id')->orWhere('variant_id', '=', '');
	}

	public function getColorAttribute()
	{
		$value = $this->attributes['color'];
		return ($value == '-' || $value == false) ? null : $value;
	}

	public function getSizeAttribute()
	{
		$value = $this->attributes['size'];
		return ($value == '-' || $value == false) ? null : $value;
	}

	public function getSurfaceAttribute()
	{
		$value = $this->attributes['surface'];
		return ($value == '-' || $value == false) ? null : $value;
	}

	public function getImagePreview1UrlAttribute()
	{
		if (!$this->attributes['image_preview_1'])
		{
			return URL::asset("themes/admin/assets/images/placeholder/image-not-found-105.jpg");
		}

		$path = ltrim($this->attributes['image_preview_1'], '/');
		$domain = rtrim(Config::get("supplychain.url"), '/');

		$path = str_replace('product/scapi/', '', $path);
		return $domain.'/'.$path;
	}
}
