<?php

class PromotionsController extends AdminController {

    /**
     * New constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Manage Campaign', URL::to('campaigns'));

        $this->theme->breadcrumb()->add('Manage Promotion', URL::previous());
    }

    public function missingMethod($parameters = array())
    {
        // check first parameter is numeric - call get index
        if ( ! empty($parameters[0]) && is_numeric($parameters[0]))
        {
            $id = $parameters[0];
            return $this->getIndex($id);
        }
    }

    protected function generateCode($length)
    {
        $pool = "2345679ABCDEFGHJKMNPRSTUVXYZ";
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    /**
     * List all brands and policies.
     *
     * @return string
     */
    public function getIndex($campaignId = null)
    {
        if ($campaignId)
        {
            $campaign = Campaign::findOrFail($campaignId);
            $promotions = Promotion::whereCampaignId($campaignId)->get();
        } else {
            $campaign = null;
            $promotions = Promotion::all();
        }

        $this->data['campaign'] = $campaign;
        $this->data['promotions'] = $promotions;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Promotion Management');

        return $this->theme->of('promotions.index', $this->data)->render();
    }

    public function getView($id)
    {
        $this->theme->breadcrumb()->add('View', URL::current());

        $promotion = Promotion::with(array('translates', 'note', 'promotionCategory', 'promotionCodes'))->findOrFail($id);

        $this->data['promotion'] = $promotion;

        return $this->theme->of('promotions.view', $this->data)->render();
    }

    public function getCreate($campaignId = null)
    {
        if (is_null($campaignId))
        {
            return Redirect::to('campaigns');
        }

        $this->theme->breadcrumb()->add('Create', URL::current());

        $campaign = Campaign::findOrFail($campaignId);

        $categories = PromotionCategory::lists('name','enum_promotion', 'id');

        $effectsDiscountFollowingItemsDatas = Session::get('effectsDiscountFollowingItemsDatas', array());
        $effectsDiscountExcludeProductUnFollowingItemsDatas = Session::get('effectsDiscountExcludeProductUnFollowingItemsDatas', array());
        $effectsDiscountExcludeVariantUnFollowingItemsDatas = Session::get('effectsDiscountExcludeVariantUnFollowingItemsDatas', array());

        $this->theme->asset()->usePath()->add('promotions-create', 'admin/css/promotions-create.css', array('style-mws-style', 'style-mws-theme'));

        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-timepicker', 'jui/js/timepicker/jquery-ui-timepicker.min.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('promotions-create', 'admin/js/promotions_create.js', 'jquery');

        // popup manage items plugin
        $this->theme->asset()->container('footer')->usePath()->add('popup-manage-items', 'admin/js/popup-manage-items.js', 'jquery');

        $view = compact('campaign', 'campaignId', 'categories', 'effectsDiscountFollowingItemsDatas', 'effectsDiscountExcludeProductUnFollowingItemsDatas', 'effectsDiscountExcludeVariantUnFollowingItemsDatas');

        return $this->theme->of('promotions.create', $view)->render();
    }

    public function postCreate($campaignId = null)
    {
        $campaign = Campaign::findOrFail($campaignId);

        // error array list
        $errors = array();

        // function response error
        $errorResponse = function($error = null) use (&$errors, &$campaignId)
        {
            if (! is_null($error))
            {
                $errors[] = $error;
            }

            $effectsDiscountFollowingItemsDatas = array();
            $effectsDiscountExcludeProductUnFollowingItemsDatas = array();
            $effectsDiscountExcludeVariantUnFollowingItemsDatas = array();

            if (Input::has('effects.discount.following_items'))
            {
                switch (Input::get('effects.discount.which'))
                {
                    case 'variant':
                        $model = new ProductVariant;
                        break;
                    case 'product':
                        $model = new Product;
                        break;
                    case 'brand':
                        $model = new Brand;
                        break;
                    case 'collection':
                        $model = new Collection;
                        break;
                }
                $model::whereIn('pkey', explode(',', Input::get('effects.discount.following_items')))->get()
                ->each(function($model) use (&$effectsDiscountFollowingItemsDatas)
                {
                    $effectsDiscountFollowingItemsDatas[] = array(
                        'pkey' => $model->pkey,
                        'title' => $model->title
                    );
                });
            }
            Session::put('effectsDiscountFollowingItemsDatas', $effectsDiscountFollowingItemsDatas);

            if (Input::has('effects.discount.exclude_product.un_following_items'))
            {
                Product::whereIn('pkey', explode(',', Input::get('effects.discount.exclude_product.un_following_items')))->get()
                ->each(function($model) use (&$effectsDiscountExcludeProductUnFollowingItemsDatas)
                {
                    $effectsDiscountExcludeProductUnFollowingItemsDatas[] = array(
                        'pkey' => $model->pkey,
                        'title' => $model->title
                    );
                });
            }
            Session::put('effectsDiscountExcludeProductUnFollowingItemsDatas', $effectsDiscountExcludeProductUnFollowingItemsDatas);

            if (Input::has('effects.discount.exclude_variant.un_following_items'))
            {
                ProductVariant::whereIn('pkey', explode(',', Input::get('effects.discount.exclude_variant.un_following_items')))->get()
                ->each(function($model) use (&$effectsDiscountExcludeVariantUnFollowingItemsDatas)
                {
                    $effectsDiscountExcludeVariantUnFollowingItemsDatas[] = array(
                        'pkey' => $model->pkey,
                        'title' => $model->title
                    );
                });
            }
            Session::put('effectsDiscountExcludeVariantUnFollowingItemsDatas', $effectsDiscountExcludeVariantUnFollowingItemsDatas);

            return Redirect::to('promotions/create/'.$campaignId)
                ->withInput()
                ->withErrors($errors);
        };

        $promotion = new Promotion;

        if (Input::has('translate'))
        {
            $promotion->setTranslate('description', Input::get('translate.description'));
        }


        $conditions = Input::get('conditions');

        if (
            Input::get('promotion_category') == 'coupon_code'
            || Input::get('promotion_category') == 'cash_voucher'
        )
        {
            if (
                empty($conditions['promotion_code'])
                || ! is_array($conditions['promotion_code'])
            )
            {
                $errors[] = "Please define promotion code conditions.";
            }
        }

        // promotion category is trueyou so promotion must has code
        // and has trueyou condition
        if ( Input::get('promotion_category') == 'trueyou')
        {
            if( ! Input::get('code') )
            {
                $errors[] = "Please input Code";
            }

            if (empty($conditions['trueyou']) || ! is_array($conditions['trueyou']))
            {
                $errors[] = "Please define trueyou conditions.";
            }
        }

        // important! start_date must after start_date from campaign
        if (strtotime($campaign->start_date) > strtotime(Input::get('start_date')))
        {
            $errors[] = "Start date of period must after campaign's start date. Campaign's start date is {$campaign->start_date}.";
        }

        // If condition trueyou is selected,
        // it's must has red card or black card in it.
        if (! empty($conditions['trueyou']) && is_array($conditions['trueyou']))
        {
            foreach ($conditions['trueyou'] as $key => $trueyouCondition) {
                $truecardAvaliable = array("red_card", "black_card");
                if (! in_array(array_get($trueyouCondition, 'type'), $trueyouCondition))
                {
                    $errors[] = "Please select trueyou card.";
                }
            }
        }

        if (! empty($conditions['promotion_code']) && is_array($conditions['promotion_code']))
        {
            foreach ($conditions['promotion_code'] as $key => $promotionCodeCondition) {
                // check promotion format
                $promotionCodeFormat = array_get($promotionCodeCondition, 'format');
                if (in_array($promotionCodeFormat, array("single_code", "multiple_code")))
                {
                    if ($promotionCodeFormat == "single_code")
                    {
                        // promotion code is single code
                        // so used times must be define
                        // promotion code should can use many times
                        $usedTimes = array_get($promotionCodeCondition, 'single_code.used_times');
                        if (! $usedTimes || ! is_numeric($usedTimes))
                        {
                            $errors[] = "You must set usable time of single code";
                        }
                    }
                    if ($promotionCodeFormat == "multiple_code")
                    {
                        // promotion codes are multiple code
                        // so each promotion code can use once
                        $count = array_get($promotionCodeCondition, 'multiple_code.count');
                        if (! $count || ! is_numeric($count))
                        {
                            $errors[] = "You must set amount of unique code to create.";
                        }

                        // when promotion code is multiple code
                        // and user want to use auto generate code method for create code
                        // user must assign length of code that want to created
                        if (array_get($promotionCodeCondition, 'code') == "auto")
                        {
                            $endWith = array_get($promotionCodeCondition, 'end_with');
                            if (! $endWith || ! is_numeric($endWith) || (is_numeric($count) && $endWith < strlen($count)))
                            {
                                $errors[] = "You must set promotion code length more or equal to amount of unique code.";
                            }
                        }
                    }
                }
                else
                {
                    $errors[] = "Please select type of promotion code.";
                }

                // after check method for create code...

                if (array_get($promotionCodeCondition, 'code') == "auto")
                {
                    // promotion code is auto gerenate so....
                    $startWith = array_get($promotionCodeCondition, 'start_with');
                    if (strlen($startWith) < 3 || strlen($startWith) > 5)
                    {
                        $errors[] = "Code prefix must be 3-5 characters";
                    }

                    // promotion code must have length for auto generate
                    $endWith = array_get($promotionCodeCondition, 'end_with');
                    if (! $endWith || ! is_numeric($endWith))
                    {
                        $errors[] = "You must set promotion code length as numeric for auto generate code.";
                    }
                }
            }
        }

        // trim space, blank value
        $effects = Input::get('effects');

        // allow character to be promotion code
        // 2345679ABCDEFGHJKMNPRSTUVXYZ


        $effectsAvaliable = array("discount", "free", "free_shipping");

        $effectsFiltered = array_intersect((Input::get('effects.type') ?: array()), $effectsAvaliable);

        // this promotion should have least one effect.
        if (count($effectsFiltered) < 1)
        {
            $errors[] = "Please select at least one effect for this promotion.";
        }

        // if effects have discount. user must define discount price or percent
        if (in_array('discount', (Input::get('effects.type') ?: array())))
        {
            if (
                empty($effects['discount']['type'])
                || ! in_array($effects['discount']['type'], array("price", "percent"))
            )
            {
                $errors[] = "Please select method of discount";
            }
            else
            {
                if ($effects['discount']['type'] == "price")
                {
                    if (
                        empty($effects['discount']['baht'])
                        || ! is_numeric($effects['discount']['baht'])
                    )
                    {
                        $errors[] = "Please input discount price.";
                    }
                    else
                    {
                        $effects['discount']['baht'] = intval($effects['discount']['baht']);
                        if ($effects['discount']['baht'] < 0)
                        {
                            $errors[] = "You cannot use negative number as discount price.";
                        }
                    }
                }

                if ($effects['discount']['type'] == "percent")
                {
                    if (
                        empty($effects['discount']['percent'])
                        || ! is_numeric($effects['discount']['percent'])
                    )
                    {
                        $errors[] = "Please input discount percent.";
                    }
                    else
                    {
                        $effects['discount']['percent'] = intval($effects['discount']['percent']);
                        if ($effects['discount']['percent'] > 100)
                        {
                            $errors[] = "You cannot discount item more tha 100 percent.";
                        }

                        if ($effects['discount']['percent'] < 0)
                        {
                            $errors[] = "You cannot use negative number as discount value.";
                        }
                    }
                }
            }
        }

        // checking following item in effect is valid
        $effectType = array('discount', 'free');
        foreach ($effectType as $type) {

            $effect = array_get($effects, "{$type}");
            if (! $effect || ! is_array($effect))
            {
                continue;
            }

            if (
                ! in_array(array_get($effects, "{$type}.on"), array("cart", "same_product", "following"))
                && Input::get('promotion_category') == 'cash_voucher'
            ) {
                $errors[] = "Please select target of {$type} effect.";
            }

            if (array_get($effects, "{$type}.on") == 'following')
            {
                $table = null;
                switch ($effects[$type]['which']) {
                    case 'variant':
                        $table = 'variants';
                        break;

                    case 'product':
                        $table = 'products';
                        break;

                    case 'brand':
                        $table = 'brands';
                        break;

                    case 'collection':
                        $table = 'collections';
                        break;

                    // for injection case or new select value that logic don't support.
                    // if it new select value. pls write new case in this switch.
                    default:
                        $errors[] = "You can't set ".$effects[$type]['which']." as type of following_items for ".$effects[$type]['on']." items.";
                        break;
                }

                if ($table)
                {
                    if (! empty($effects[$type]['following_items']))
                    {
                        getFilterPkey($effects[$type]['following_items'], $table);
                    }
                }

                if (
                    empty($effects[$type]['following_items'])
                    || ! is_array($effects[$type]['following_items'])
                    || count($effects[$type]['following_items']) < 1
                )
                {
                    $errors[] = "You must select following items when user get ".$effects[$type]['on']." items";
                }

                if (! empty($effects[$type]['exclude_product']['un_following_items']))
                {
                    getFilterPkey($effects[$type]['exclude_product']['un_following_items'], 'products');
                }
                else
                {
                    $effects[$type]['exclude_product']['un_following_items'] = array();
                }


                if (! empty($effects[$type]['exclude_variant']['un_following_items']))
                {
                    getFilterPkey($effects[$type]['exclude_variant']['un_following_items'], 'variants');
                }
                else
                {
                    $effects[$type]['exclude_variant']['un_following_items'] = array();
                }

            }
        }

        if (count($errors))
        {
            return $errorResponse();
        }

        $promotion->campaign_id = $campaignId;
        //$promotion->promotion_category_id = Input::get('promotion_category_id');
        $promotion->promotion_category = Input::get('promotion_category');
        $promotion->name = Input::get('name');
        $promotion->code = Input::get('code');
        $promotion->description = Input::get('description');
//      $promotion->note = Input::get('note');
        $promotion->start_date = Input::get('start_date');
        $promotion->end_date = Input::get('end_date');
        $promotion->budget = (Input::get('limitations.limit_budget'))?Input::get('limitations.budget'):0;
//      $promotion->used_budget = Input::get('type');
//      $promotion->used_time = Input::get('type');
//      $promotion->used_users = Input::get('type');
//      $promotion->gifted_items = Input::get('type');
        $promotion->conditions = Input::get('conditions');
        $promotion->effects = $effects;
        $promotion->status = Input::get('status');

        if ( ! $promotion->save())
        {
            return Redirect::to('promotions/create/'.$campaignId)->withInput()->withErrors($promotion->errors());
        }

        // use promotion_code
        if (in_array(Input::get('promotion_category'), array('coupon_code', 'cash_voucher')))
        {
            if (Input::get('conditions.promotion_code.0.format') == 'single_code')
            {
                $codeCount = 1;
                $used_times = (int) Input::get('conditions.promotion_code.0.single_code.used_times');
            }
            else // multiple_code
            {
                $codeCount = (int) Input::get('conditions.promotion_code.0.multiple_code.count');
                $used_times = 1;
            }

            $codes = array();

            if (Input::get('conditions.promotion_code.0.code') == 'auto')
            {
                $startWith = Input::get('conditions.promotion_code.0.start_with');
                $endWith = (int) Input::get('conditions.promotion_code.0.end_with');

                // code must be at least 12 character
                $prefixLength = strlen($startWith);
                $suffixLength = $endWith;
                if ($prefixLength + $suffixLength < 12)
                {
                    $endWith = 12 - $prefixLength;
                }
                else if ($prefixLength + $suffixLength > 20)
                {
                    $endWith = 20 - $prefixLength;
                }

                // generate code
                for (;count($codes)<$codeCount;)
                {
                    // generate code
                    $generatedCode = strtoupper($startWith.$this->generateCode($endWith));

                    $promotionCode = PromotionCode::validCode($generatedCode)->first();

                    if (! $promotionCode)
                    {
                        $codes[$generatedCode] = $used_times;
                    }
                }
            }
            else // custom
            {
                //
            }

            // save code
            foreach ($codes as $key => $val)
            {
                $promotionCode = new PromotionCode;
                $promotionCode->code = $key;
                $promotionCode->avaliable = $val;
                $promotionCode->type = Input::get('promotion_category');
                $promotionCode->status = 'activate';
                $promotion->promotionCodes()->save($promotionCode);
            }
        }

        if (Input::has('note'))
        {
            $note = new Note;
            $note->detail = Input::get('note');
            $promotion->note()->save($note);
        }

        Session::forget('effectsDiscountFollowingItemsDatas');
        Session::forget('effectsDiscountExcludeProductUnFollowingItemsDatas');
        Session::forget('effectsDiscountExcludeVariantUnFollowingItemsDatas');

        // Rebuild
        $promotion->rebuildPromotion();

        return Redirect::to('promotions/'.$campaignId);
    }

    public function getCheckCode($code)
    {
        $code = PromotionCode::checkValidCode($code);

        return (boolean) $code;
    }

    public function getEdit($promotionId = null)
    {
        $promotion = Promotion::findOrFail($promotionId);
        $promotionArray = $promotion->toArray();

        $this->theme->breadcrumb()->add('Edit', URL::current());

        if (Input::has('x')) sd($promotionArray);

        $effectsDiscountFollowingItemsDatas = array();
        $effectsDiscountExcludeProductUnFollowingItemsDatas = array();
        $effectsDiscountExcludeVariantUnFollowingItemsDatas = array();

//        $effectsDiscountFollowingItemsDatas = array_get($promotionArray, 'effects.discount.following_items', array());
//        $effectsDiscountExcludeProductUnFollowingItemsDatas = array_get($promotionArray, 'effects.discount.exclude_product', array());
//        $effectsDiscountExcludeVariantUnFollowingItemsDatas = array_get($promotionArray, 'effects.discount.exclude_variant', array());

        // if ($promotionArray['effects']['discount']['on'] == 'following')
        // {
        //     switch ($promotionArray['effects']['discount']['which'])
        //     {
        //         case 'variant':
        //             $model = new ProductVariant;
        //             break;
        //         case 'product':
        //             $model = new Product;
        //             break;
        //         case 'brand':
        //             $model = new Brand;
        //             break;
        //     }

        //     if (array_get($promotionArray, 'effects.discount.following_items'))
        //     {
        //         $model::whereIn('pkey', $promotionArray['effects']['discount']['following_items'])->get()
        //         ->each(function($model) use (&$effectsDiscountFollowingItemsDatas)
        //         {
        //             $effectsDiscountFollowingItemsDatas[] = array(
        //                 'pkey' => $model->pkey,
        //                 'title' => $model->title
        //             );
        //         });
        //     }


        //     if (isset($promotionArray['effects']['discount']['exclude_product']['un_following_items']))
        //     {
        //         Product::whereIn('pkey', explode(',', $promotionArray['effects']['discount']['exclude_product']['un_following_items']))->get()
        //         ->each(function($model) use (&$effectsDiscountExcludeProductUnFollowingItemsDatas)
        //         {
        //             $effectsDiscountExcludeProductUnFollowingItemsDatas[] = array(
        //                 'pkey' => $model->pkey,
        //                 'title' => $model->title
        //             );
        //         });
        //     }

        //     if (isset($promotionArray['effects']['discount']['exclude_variant']['un_following_items']))
        //     {
        //         ProductVariant::whereIn('pkey', explode(',', $promotionArray['effects']['discount']['exclude_variant']['un_following_items']))->get()
        //         ->each(function($model) use (&$effectsDiscountExcludeVariantUnFollowingItemsDatas)
        //         {
        //             $effectsDiscountExcludeVariantUnFollowingItemsDatas[] = array(
        //                 'pkey' => $model->pkey,
        //                 'title' => $model->title
        //             );
        //         });
        //     }
        // }

        //$categories = PromotionCategory::lists('name','enum_promotion', 'id');

        $this->theme->asset()->usePath()->add('promotions-create', 'admin/css/promotions-create.css', array('style-mws-style', 'style-mws-theme'));

        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-timepicker', 'jui/js/timepicker/jquery-ui-timepicker.min.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('promotions-create', 'admin/js/promotions_create.js', 'jquery');

        // popup manage items plugin
       $this->theme->asset()->container('footer')->usePath()->add('popup-manage-items', 'admin/js/popup-manage-items.js', 'jquery');

        $view = compact('promotion', 'effectsDiscountFollowingItemsDatas', 'effectsDiscountExcludeProductUnFollowingItemsDatas', 'effectsDiscountExcludeVariantUnFollowingItemsDatas');

        return $this->theme->of('promotions.edit', $view)->render();
    }

    public function postEdit($id = 0)
    {
        $promotion = Promotion::findOrFail($id);
        if (Input::has('translate'))
        {
            $promotion->setTranslate('description', Input::get('translate.description'));
        }

        // // trim space, blank value
        // $effects = Input::get('effects');

        // // checking following item in effect is valid
        // $effectType = array('discount', 'free');
        // foreach ($effectType as $type) {
        //     if (isset($effects[$type]['on']))
        //     {
        //         if ($effects[$type]['on'] == 'following')
        //         {
        //             $effects[$type]['following_items'] = array_values(array_filter(preg_split('/[ ]*,[ ]*/', trim($effects[$type]['following_items']))));
        //             if ( ! empty($effects[$type]['following_items']))
        //             {
        //                 if ($effects[$type]['which'] == 'variant')
        //                 {
        //                     // if discount on variant, we will check with inventory id
        //                     $effects[$type]['following_items'] = ProductVariant::whereIn('inventory_id', $effects[$type]['following_items'])->lists('inventory_id');
        //                 } else {
        //                     // another such as product or brand or collection will check with pkey
        //                     $effects[$type]['following_items'] = PCMSKey::whereIn('code', $effects[$type]['following_items'])->lists('code');
        //                 }
        //             }
        //         }
        //     }
        // }

        //$promotion->promotion_category_id = Input::get('promotion_category_id');
        $promotion->promotion_category = Input::get('promotion_category');
        $promotion->name = Input::get('name');
        //$promotion->code = Input::get('code');
        $promotion->description = Input::get('description');
        // $promotion->start_date = Input::get('start_date');
        $promotion->end_date = Input::get('end_date');
        // $promotion->budget = (Input::get('limitations.limit_budget'))?Input::get('limitations.budget'):0;
        // $promotion->conditions = Input::get('conditions');
        // $promotion->effects = $effects;
        $promotion->status = Input::get('status');

        if ( ! $promotion->save())
        {
            return Redirect::to('promotions/create/'.$campaignId)->withInput()->withErrors($promotion->errors());
        }

        // // use promotion_code
        // if (Input::has('conditions.promotion_code'))
        // {
        //     if (Input::get('conditions.promotion_code.0.format') == 'single_code')
        //     {
        //         $codeCount = 1;
        //         $used_times = (int) Input::get('conditions.promotion_code.0.single_code.used_times');
        //     }
        //     else // multiple_code
        //     {
        //         $codeCount = (int) Input::get('conditions.promotion_code.0.multiple_code.count');
        //         $used_times = 1;
        //     }

        //  $codes = array();

        //     if (Input::get('conditions.promotion_code.0.code') == 'auto')
        //     {
        //         $startWith = Input::get('conditions.promotion_code.0.start_with');
        //         $endWith = (int) Input::get('conditions.promotion_code.0.end_with');

        //         // code must be at least 12 character
        //         $prefixLength = strlen($startWith);
        //         $suffixLength = $endWith;
        //         if ($prefixLength + $suffixLength < 12)
        //         {
        //             $endWith = 12 - $prefixLength;
        //         }
        //         else if ($prefixLength + $suffixLength > 20)
        //         {
        //             $endWith = 20 - $prefixLength;
        //         }

        //         // generate code
        //         for (;count($codes)<$codeCount;)
        //         {
        //             // generate code
        //             $generatedCode = $startWith.$this->generateCode($endWith);

        //             $promotionCode = PromotionCode::validCode($generatedCode)->first();

        //             if (! $promotionCode)
        //             {
        //                 $codes[$generatedCode] = $used_times;
        //             }
        //         }
        //     }
        //     else // custom
        //     {
        //         //
        //     }

        //     // save code
        //     foreach ($codes as $key => $val)
        //     {
        //         $promotionCode = new PromotionCode;
        //         $promotionCode->code = $key;
        //         $promotionCode->avaliable = $val;
        //         $promotionCode->type = Input::get('promotion_category');
        //         $promotionCode->status = 'activate';
        //         $promotion->promotionCodes()->save($promotionCode);
        //     }
        // }

        // if (Input::has('note'))
        // {
        //     $note = new Note;
        //     $note->detail = Input::get('note');
        //     $promotion->note()->save($note);
        // }

        // Rebuild
        $promotion->rebuildPromotion();

        return Redirect::to('promotions/view/'.$id.'?campaign=true');
    }

    protected function getSafeCodeCharacters()
    {
        return str_split("2345679ABCDEFGHJKMNPRSTUVXYZ");
    }

}