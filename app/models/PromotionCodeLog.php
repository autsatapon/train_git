<?php

class PromotionCodeLog extends Harvey
{

    public function promotionCode()
    {
        return $this->belongsTo('PromotionCode');
    }

    public function order()
    {
        return $this->belongsTo('Order');
    }
	
	public function promotion()
	{
		return $this->belongsTo('Promotion','promotion_code_id');
	}
	
	public function orderTrans()
	{
		return $this->belongsToMany('OrderTransaction','order_id');
	}
}