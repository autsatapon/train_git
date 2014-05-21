<?php

class ShippingBox extends PCMSModel {

	public static $autoKey = false;
	
	public static $labels = array(
		'name' => 'Box Name',
		'weight' => 'Weight (g)',
	);
	
	//set rule.
	public static $rules = array(
		'name' => 'required',
		'weight' => 'required|integer',
		'price' => 'required|numeric',
	);

} 