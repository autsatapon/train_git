<?php

class MigratePromotionUsed extends Eloquent {

    public $table = 'raw_promotion_used';

    // protected $with = array('promotionCode');

    protected $hidden = array(
        'id',
        'discount_group_id',
        'code',
        'sso_id',
        // 'order_id',
        'product_id',
        'inventory_id',
        'discount_price',
        'used_date',
        'used_status',
        'updated_date',
        'migrate_status',
        'error',
        'created_date',
        'updated_date',
        'created_at',
        'updated_at'
        );

    protected $appends = array(
        'promotion_code_id',
        // 'order_id',
        );

    public static $maps = array();

        /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        if (empty(static::$maps['order']))
        {
            $orders   = DB::table('order_maps')->get();

            foreach ($orders as $order)
            {
                static::$maps['order'][$order->itruemart_id] = array(
                    'pcms_id' => $order->pcms_id,
                    'pkey'    => $order->pkey
                );
            }
        }


        if (empty(static::$maps['promotion_code']))
        {
            $promotion_codes   = DB::table('promotion_code_maps')->get();

            foreach ($promotion_codes as $promotion_code)
            {
                static::$maps['promotion_code'][$promotion_code->code] = array(
                    'pcms_id' => $promotion_code->pcms_id
                );
            }
        }


        //sd(static::$maps);
    }

    public function getOrderIdAttribute($value)
    {
        if ( ! isset(static::$maps['order'][$value]))
        {
            throw new Exception("Order not found. {$value}");
        }

        return static::$maps['order'][$value]['pcms_id'];
    }


    public function getPromotionCodeIdAttribute()
    {
        if ( ! isset(static::$maps['promotion_code'][$this->code]))
        {
            throw new Exception("Promotion code not found. {$this->code}");
        }

        return static::$maps['promotion_code'][$this->code]['pcms_id'];
    }

}