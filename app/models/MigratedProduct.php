<?php

class MigratedProduct extends Eloquent {
	public $timestamps    = false;
	protected $fillable = array('sku_code' ,'product_id' ,'inventory_id' ,'material_code' ,'shop_id' ,'brand_name' ,'barcode' ,'title' ,'color' ,'size' ,'normal_price' ,'special_price' ,'margin' ,'option' ,'stock' ,'vendor_code' ,'vendor_type' ,'vendor_stock' ,'product_status' ,'create_date' ,'title_eng' ,'key_feture_thai' ,'key_feture_eng' ,'description_thai' ,'description_eng' ,'color_code' ,'color_image' ,'size_code' ,'size_image' ,'texture' ,'texture_code' ,'texture_image' ,'product_image_original' ,'product_image_big' ,'product_image_medium' ,'product_image_thumb' ,'installment' ,'installment_period' ,'tags' ,'suggestions' ,'brand_id' ,'category_id' ,'category_name_thai' ,'category_name_eng');
	
	protected $table = 'migrated_product';

}