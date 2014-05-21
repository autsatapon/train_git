<?php

class OrderStatus {

	public static $code = array(
		'draft' => 0,
		'waiting' => 10,
		'new' => 20,
		'gift-rejected' => 24,
		'no-gift' => 30,
		'added-gift' => 31,
		'gift-checked' => 40,
		'ready' => 50,
		'processing' => 51,
		'sending' => 60,
		'sent' => 70,
		'delivered' => 80,
		'complete' => 90,
		'done' => 91,
		'unshipable' => 160,
		'refund' => 170
	);
	
	public static function getStatus($code)
	{
		return Cache::remember("orderStatus-en-$code", 86400, function() use ($code)
		{
			return StatusDict::where('status_type', 'order_status')->where('status_id', $code)->pluck('english');
		});
	}

	public static function getTHStatus($code)
	{
		return Cache::remember("orderStatus-th-$code", 86400, function() use ($code)
		{
			return StatusDict::where('status_type', 'order_status')->where('status_id', $code)->pluck('thai');
		});
	}

}
