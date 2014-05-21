<?php

class ValidPromotion extends Harvey {

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    public static $rules = array(
        'promotion_id' => 'required',
        'promotionable_id' => 'required',
        'promotionable_type' => 'required',
        /* 'brandlogo' => 'image|max:2000', */
    );

    public function promotionable()
    {
        return $this->morphTo();
    }

    public function promotion()
    {
        return $this->belongsTo('Promotion');
    }

    public function getMetaAttribute($val)
    {
        return json_decode($val, true);
    }

    public function setMetaAttribute($value)
    {
        $this->attributes['meta'] = json_encode($value);
    }

    // public function getPromotionCodes()
    // {
    //     return (array) array_get($this->attributes, 'meta.promotion_codes');
    // }

    // public function addPromotionCode($promotionCode)
    // {
    //     if ($this->attributes['promotion_id'] != $promotionCode->promotion_id)
    //     {
    //         throw new Exception("Promotion code don't belong to current promotion.");
    //     }

    //     $currentPromotionCodes = $this->getPromotionCodes();

    //     foreach ($currentPromotionCodes as $key => $currentPromotionCode) {
    //         if (array_get($currentPromotionCode, 'type') == $promotionCode->type)
    //         {
    //             // have same type so return false
    //             return false;
    //         }
    //     }

    //     // don't have same type so we can add it
    //     if (! isset($this->attributes['meta']))
    //     {
    //         $this->attributes['meta'] = array();
    //     }

    //     if (! isset($this->attributes['meta']['promotion_codes']))
    //     {
    //         $this->attributes['meta']['promotion_codes'] = array();
    //     }

    //     $this->attributes['meta']['promotion_codes'][] = array(
    //         'type' => $promotionCode->type,
    //         'id'   => $promotionCode->getKey(),
    //         'code' => $promotionCode->code
    //     );

    //     return true;
    // }

    // public function removePromotionCode($promotionCode)
    // {
    //     $currentPromotionCodes = $this->getPromotionCodes();

    //     foreach ($currentPromotionCodes as $key => $currentPromotionCode) {
    //         if (array_get($currentPromotionCode, 'code') == $promotionCode)
    //         {
    //             unset($this->attributes['meta']['promotion_codes'][$key]);
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    // public function __set($key, $value)
    // {
    //     if ($key == 'meta')
    //     {
    //         throw new Exception('Meta attribute can not set direcly.');
    //     }

    //     parent::__set($key, $value);
    // }

    // public function __unset($key)
    // {
    //     if ($key == 'meta')
    //     {
    //         throw new Exception('Meta attribute can not unset direcly.');
    //     }

    //     parent::__unset($key);
    // }

}