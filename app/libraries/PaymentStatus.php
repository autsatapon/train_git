<?php

class PaymentStatus {

	public static $code = array(
		'waiting' => 1,
		'checking' => 2,
		'paid' => 3,
		'refund' => 6
	);
	
	public static function getStatus($code)
	{
		return Cache::remember("paymentStatus-en-$code", 86400, function() use ($code)
		{
			return StatusDict::where('status_type', 'payment_status')->where('status_id', $code)->pluck('english');
		});
	}

	public static function getTHStatus($code)
	{
		return Cache::remember("paymentStatus-th-$code", 86400, function() use ($code)
		{
			return StatusDict::where('status_type', 'payment_status')->where('status_id', $code)->pluck('thai');
		});
	}

}
