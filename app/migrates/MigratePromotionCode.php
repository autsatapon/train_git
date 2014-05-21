<?php

class MigratePromotionCode extends Eloquent {

    public $table = 'raw_promotion_code';

    // protected $with = array('promotion');

    protected $hidden = array(
        'promotion',
        'count',
        'angpao_used',
        'sso_id',
        'id'
        );

    protected $appends = array(
        // 'promotion_code_id',
        // 'code',
        'avaliable',
        'used',
        'type',
        'status',
        'promotion_id'
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

        if (empty(static::$maps['promotion']))
        {
            $promotions   = DB::table('promotion_maps')->get();

            foreach ($promotions as $promotion)
            {
                static::$maps['promotion'][$promotion->itruemart_id] = array(
                    'pcms_id' => $promotion->pcms_id,
                    'pkey'    => $promotion->pkey
                );
            }
        }


        //sd(static::$maps);
    }


    public function getPromotionIdAttribute()
    {
        if ( ! isset(static::$maps['promotion'][$this->discount_group_id]))
        {
            return null;
        }

        return static::$maps['promotion'][$this->discount_group_id]['pcms_id'];
    }

    public function promotion()
    {
        return $this->belongsTo('MigratePromotionGroup', 'discount_group_id');
    }

    // public function getPromotionCodeIdAttribute()
    // {
    //     if (! $this->promotion)
    //     {
    //         throw new Exception('Promotion Code: Promotion isn\'t exists. ('.$this->getKey().')');
    //     }

    //     return $this->promotion->getKey();
    // }

    public function getAvaliableAttribute()
    {
        if (! $this->promotion)
        {
            throw new Exception('Avaliable: Promotion isn\'t exists. ('.$this->getKey().')');
        }

        if (! in_array($this->promotion->type, array('unique', 'single')))
        {
            throw new Exception('Avaliable: Promotion type isn\'t unique or single. ('.$this->getKey().')');
        }

        if ($this->promotion->type == 'single')
        {
            // can use many
            return $this->promotion->code_amount;
        }
        else
        {
            // type is unique
            // can use once
            return 1;
        }
    }

    public function getUsedAttribute()
    {
        if (! $this->promotion)
        {
            throw new Exception('Used: Promotion isn\'t exists. ('.$this->getKey().')');
        }

        if (! in_array($this->promotion->type, array('unique', 'single')))
        {
            throw new Exception('Used: Promotion type isn\'t unique or single. ('.$this->getKey().')');
        }

        $used = $this->avaliable - $this->count;

        return $used > 0 ? $used : 0;
    }

    public function getTypeAttribute()
    {
        if (! $this->promotion)
        {
            throw new Exception('Type: Promotion isn\'t exists. ('.$this->getKey().')');
        }

        if (! $this->promotion->promotion_category)
        {
            throw new Exception('Type: Promotion category is empty. ('.$this->getKey().')');
        }

        return $this->promotion->promotion_category;
    }

    public function getStatusAttribute()
    {
        return $this->avaliable > $this->used ? 'activate' : 'deactivate';
    }

}