<?php

class MigratePromotionItem extends Eloquent {

    public $table = 'raw_promotion_item';

    public $appends = array('pcms_id', 'pkey');

    public static $maps = array(
        'brand'   => array(),
        'product' => array()
    );

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);


        if (empty(static::$maps['brand']))
        {
            $brands   = DB::table('brand_maps')->get();
            foreach ($brands as $brand)
            {
                static::$maps['brand'][$brand->itruemart_id] = array(
                    'pcms_id' => $brand->pcms_id,
                    'pkey'    => $brand->pkey
                );
            }
        }

        if (empty(static::$maps['product']))
        {
            $products = DB::table('product_maps')->get();
            foreach ($products as $product)
            {
                static::$maps['product'][$product->itruemart_id] = array(
                    'pcms_id' => $product->pcms_id,
                    'pkey'    => $product->pkey
                );
            }
        }

        if (empty(static::$maps['category']))
        {
            $collections = DB::table('category_maps')->get();
            foreach ($collections as $collection)
            {
                static::$maps['category'][$collection->itruemart_id] = array(
                    'pcms_id' => $collection->pcms_id,
                    'pkey'    => $collection->pkey
                );
            }
        }


        //sd(static::$maps);
    }



    public function getPcmsIdAttribute()
    {
        if ( ! isset(static::$maps[$this->item_type][$this->item_id]))
        {
            return null;
        }

        return static::$maps[$this->item_type][$this->item_id]['pcms_id'];
    }

    public function getPkeyAttribute()
    {
        if ( ! isset(static::$maps[$this->item_type][$this->item_id]))
        {
            return null;
        }

        return static::$maps[$this->item_type][$this->item_id]['pkey'];
    }

}