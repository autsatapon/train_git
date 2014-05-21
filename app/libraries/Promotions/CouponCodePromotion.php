<?php namespace Promotions;

use Cart;

class CouponCodePromotion extends BaseCodePromotion {

    protected $eventPriority = 30;

    protected $promotionCodeType = "coupon_code";

    public function isActive()
    {
        return true;
    }

}