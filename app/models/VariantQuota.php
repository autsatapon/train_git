<?php

class VariantQuota extends Eloquent {

	protected $table = 'variant_quotas';
	protected $softDelete = true;

	public function variantLot()
	{
		return $this->belongsTo('VariantLot', 'variant_lot_id');
	}

	public function app()
	{
		return $this->belongsTo('PApp', 'app_id');
	}

	public function getRemainingAttribute()
	{
		return $this->quantity - (intval($this->hold_quantity) + intval($this->sold_quantity));
	}

}