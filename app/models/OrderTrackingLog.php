<?php

class OrderTrackingLog extends Eloquent {

	protected $guarded = array();

	protected $dates = array('previous_sla_time_at');

	public static function boot()
	{
		parent::boot();

		static::saving(function($model)
		{
			$user = Sentry::getUser();
			$model->actor_id = ($user!=false ? $user->id : null);
		});
	}

	public function actor()
	{
		return $this->belongsTo('User', 'actor_id');
	}


	
	
	
	public function getOrderStatusThAttribute()
	{
		/*
		if (is_numeric($this->order_status))
		{
			return DB::table('payment_status')->where('id', '=', $this->order_status)->pluck('thai');
		}
		*/
		return StatusDict::where('status_type','order_status')->where('english','=', $this->order_status)->pluck('thai');
	}



}