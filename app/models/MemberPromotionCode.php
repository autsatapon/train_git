<?php

class MemberPromotionCode extends Harvey
{

    public function promotionCode()
    {
        return $this->belongsTo('PromotionCode');
    }

    public function member()
    {
        return $this->belongsTo('Member');
    }

}