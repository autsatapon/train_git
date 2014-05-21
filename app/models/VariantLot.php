<?php

class VariantLot extends PCMSModel {

	public static $autoKey = false;

	public static $labels = array(
		'lot_no'	=> 'Lot ID',
	);

	public static function boot()
	{
		parent::boot();

		static::saving(function($model)
		{
			if($model->quantity > 0)
			{
				$model->deleted_at = null;
			}
			else
			{
				$model->deleted_at = date('Y-m-d H:i:s');
			}
		});
	}

	public function getRemainingAttribute()
	{
		return $this->sc_remaining;
	}

	public function quotas()
	{
		return $this->hasMany('VariantQuota', 'variant_lot_id');
	}

}