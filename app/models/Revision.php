<?php

class Revision extends Eloquent {

    public function revisionable()
    {
        return $this->morphTo();
    }

    public function scopeOfStatus($query, $status)
    {
    	if (is_array($status))
    	{
        	return $query->whereIn('status', $status);
        }

        return $query->whereStatus($status);
    }

    public function getModifiedDataAttribute($value)
    {
        $modifiedData = json_decode($this->value, TRUE);

        if (isset($modifiedData['brand_id']) && is_numeric($modifiedData['brand_id']))
        {
            $modifiedData['brand'] = Brand::find($modifiedData['brand_id']);
        }

        if (isset($modifiedData['price']))
        {
            foreach ($modifiedData['price'] as $variantId => $val)
            {
                if ($val['price'] != 0 && $val['normal_price'] == 0)
                {
                    $modifiedData['price'][$variantId]['net_price'] = $val['price'];
                    $modifiedData['price'][$variantId]['special_price'] = $val['normal_price'];
                }
                else
                {
                    $modifiedData['price'][$variantId]['net_price'] = $val['normal_price'];
                    $modifiedData['price'][$variantId]['special_price'] = $val['price'];
                }
            }
        }

        return $modifiedData;
    }

}