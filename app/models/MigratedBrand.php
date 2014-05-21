<?php

class MigratedBrand extends Eloquent {
	public $timestamps    = false;
	protected $fillable   = array('brand_id', 'name_thai', 'history_thai', 'slug_thai', 'name_eng', 'history_eng',   'slug_eng',   'vdo',   'logo_banner',   'logo_icon', 'logo_flashsale');

	protected $table = 'migrated_brand';

}