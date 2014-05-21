<?php

class PromotionCategory extends Harvey
{

	public function promotions()
	{
		return $this->hasMany('Promotion');
	}
}