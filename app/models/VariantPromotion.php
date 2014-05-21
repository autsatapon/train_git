<?php

class VariantPromotion extends Eloquent {

    protected $table = 'variant_promotion';

    public $timestamps = false;

    public function productVariant()
    {
        return $this->belongsTo('ProductVariant', 'variant_id');
    }

}

