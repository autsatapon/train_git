<?php

class VariantStyleOption extends PCMSModel {

    public static $autoKey = false;

	public $timestamps = false;

	protected $softDelete = false;

	// protected $table = 'variant_style_options_backup';

    public function variant()
    {
        return $this->belongsTo('ProductVariant');
    }

    public function styleType()
    {
        return $this->belongsTo('StyleType');
    }

    public function styleOption()
    {
        return $this->belongsTo('StyleOption');
    }

    /**
     * reletion to media_content
     */
    public function mediaContents()
    {
        return $this->morphMany('MediaContent', 'mediable');
    }

    public function getTextAttribute($val)
    {
    	$text = $this->styleOption->text;

    	return $text;
    }

    public function getPkeyAttribute($val)
    {
    	$pkey = $this->styleOption->pkey;

    	return $pkey;
    }

    public function getMetaAttribute($val)
    {
    	$meta = $this->styleOption->meta;

    	return $meta;
    }
}