<?php

class PaymentMethod extends PCMSModel {

	public static $labels = array(
		'name' => 'Payment Method Name',
		'code' => 'Code',
		'channel' => 'Channel',
		'transaction_fee' => 'Transaction Fee',
		'transaction_apply' => 'Transaction Apply'
	);

	//set rule.
	public static $rules = array(
		'name' => 'required',
		'code' => 'required',
		'transaction_fee' => 'required',
	);

}