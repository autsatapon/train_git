<?php

class OrderNote extends Harvey {

	public function order()
	{
		return $this->belongsTo('Order');
	}

}