<?php

class MigratePromotionGroup extends Eloquent {

    public $table = 'raw_promotion_group';

    protected $with = array('items');

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'discount_group_id';

    protected $hidden = array(
        'id',
        'promotion_type',
        'started_date',
        'ended_date',
        'group_type',
        'type',
        'amount',
        'type_amount',
        'price_minimum',
        'price_minimum_type',
        'code_amount',
        'angpao_status',
        'created_date',
        'updated_date',
        'activated',

        'items',
        'codes'
        );

    protected $appends = array(
        'promotion_category',
        'start_date',
        'end_date',
        'conditions',
        'effects',
        'status',
        'campaign_id'
        );

    protected static $campaignId = null;

    public function items()
    {
        return $this->hasMany('MigratePromotionItem', 'discount_group_id');
    }

    public function codes()
    {
        return $this->hasMany('MigratePromotionCode', 'discount_group_id');
    }

    public static function setCampaignId($id)
    {
        self::$campaignId = $id;
    }


    public function getCampaignIdAttribute()
    {
        // default campaign_id
        return self::$campaignId ?: 1;
    }


    public function getPromotionCategoryAttribute()
    {
        $type = $this->attributes['promotion_type'];
        if ($type == 'coupon')
        {
            return 'coupon_code';
        }

        if ($type == 'voucher')
        {
            return 'cash_voucher';
        }

        if ($type == 'trueyou')
        {
            return 'trueyou';
        }

        return 'custom';
    }

    public function getStartDateAttribute()
    {
        return $this->attributes['started_date'];
    }

    public function getEndDateAttribute()
    {
        return $this->attributes['ended_date'];
    }

    public function getConditionsAttribute()
    {
        $conditions = array();
        switch ($this->promotion_category) {
            case 'trueyou':
                // check card type
                $type = $this->type;
                if (! in_array($type, array('red', 'black')))
                {
                    throw new Exception('Condition: Trueyou isn\'t red or black. ('.$this->getKey().')');
                }

                $conditions['trueyou'] = array('0' => array());
                $conditions['trueyou'][0]['type'] = "{$type}_card";
                break;

            case 'cash_voucher':
            case 'coupon_code':
                $conditions['promotion_code'] = array('0' => array());
                $conditions['promotion_code'][0]['type'] = $this->promotion_category;
                if (! in_array($this->type, array('unique', 'single')))
                {
                    throw new Exception("Condition: Promotion code format isn't single or unique. (".$this->getKey().")");
                }
                $conditions['promotion_code'][0]['format'] = ($this->type == 'single') ? 'single_code' : 'multiple_code';
                if ($this->type == 'single')
                {
                    // has one coupon and coupon has many available use time.
                    $conditions['promotion_code'][0]['single_code'] = array('used_times' => $this->code_amount);
                }
                else
                {
                    // has many coupon and coupon has one use time.
                    $conditions['promotion_code'][0]['multiple_code'] = array('count' => $this->code_amount);
                }

                $conditions['promotion_code'][0]['code'] = 'auto';
                $conditions['promotion_code'][0]['start_with'] = '';
                $conditions['promotion_code'][0]['end_with'] = '10';
                break;
            default:
                # code...
                break;
        }

        return $conditions;
    }

    public function getEffectsAttribute()
    {
        $effects = array();

        $effects['type'] = array('discount');

        $discountType = $this->type_amount;

        if (! in_array($discountType, array('price', 'percent')))
        {

            throw new Exception('Effect: Discount type isn\'t price or percent. ('.$this->getKey().')');
        }

        $effects['discount']['type'] = ($discountType == 'price') ? 'price' : 'percent';

        if ($discountType == 'price')
        {
            $effects['discount']['baht'] = $this->amount;
        }
        else
        {
            $effects['discount']['percent'] = $this->amount;
        }

        $discountWhich = $this->group_type;

        if (! in_array($discountWhich, array('all', 'product', 'category', 'brand')))
        {
            throw new Exception('Effect: Discount which isn\'t all, product, category or brand. ('.$this->getKey().')');
        }

        if ($discountWhich == 'all')
        {
            $effects['discount']['on'] = 'cart';
        }

        if ($discountWhich == 'product')
        {
            $effects['discount']['on'] = 'following';
            $effects['discount']['which'] = 'product';

            $pkey = array_filter($this->items->lists('pkey'));

            $effects['discount']['following_items'] = array_values($pkey);

            $effects['discount']['exclude_variant'] = array();
            $effects['discount']['exclude_variant']['un_following_items'] = array();
        }

        if ($discountWhich == 'brand')
        {
            $effects['discount']['on'] = 'following';
            $effects['discount']['which'] = 'brand';

            $pkey = array_filter($this->items->lists('pkey'));

            $effects['discount']['following_items'] = array_values($pkey);

            $effects['discount']['exclude_product'] = array();
            $effects['discount']['exclude_product']['un_following_items'] = array();
        }

        if ($discountWhich == 'category')
        {
            $effects['discount']['on'] = 'following';
            $effects['discount']['which'] = 'collection';

            $pkey = array_filter($this->items->lists('pkey'));

            $effects['discount']['following_items'] = array_values($pkey);

            $effects['discount']['exclude_product'] = array();
            $effects['discount']['exclude_product']['un_following_items'] = array();
        }


        return $effects;
    }

    public function getStatusAttribute()
    {
        $activated = strtoupper($this->activated);

        return ($activated == 'Y') ? 'activate' : 'deactivate';

    }

}