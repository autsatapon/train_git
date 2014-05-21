<?php namespace Promotions;

use Cart;
use Member;
use Cache;
use App;

class TrueyouPromotion extends Promotion {

    protected $eventPriority = 20;

    public function isActive()
    {
        return true;
    }

    public function register()
    {
        $this->bindValidator('Cart.onApplyTrueyou', 'applyTrueyou');
        $this->bindValidator('Cart.onDeapplyCode', 'applyTrueyou');
    }

    public function applyEffect()
    {
        $this->bindApplyIfValid('Cart.onRemoveTrueyou', 'disableTrueyou');
        $this->bindApplyIfValid('Cart.onApplyCode', 'disableTrueyou');
        $this->bindApplyIfValid('Cart.onGetCart', 'applyDiscount');
    }

    public function applyTrueyou(Cart $cart)
    {
        // cart is installment
        if ($cart->type == 'installment')
        {
            // set valid false
            return false;
        }

        // customer type is not user
        if ($cart->customer_type != 'user')
        {
            return false;
        }

        // cart has coupon code
        if ($this->checkCouponCode($cart))
        {
            return false;
        }

        // get truecard from api
        $truecard = $this->checkTrueyouCard($cart->customer_ref_id);

        if (! $truecard)
        {
            return false;
        }

        // get trueyou condition
        $trueyouConditions = array_get($this->promotion->conditions, 'trueyou', array());

        // check card color
        foreach ($trueyouConditions as $key => $trueyouCondition) {
            if (array_get($trueyouCondition, 'type') == "{$truecard}_card")
            {
                // set promotion data
                $this->setPromotionDataToCart($cart, $truecard);

                return true;
            }
        }

        return false;
    }

    public function applyDiscount(Cart $cart)
    {
        // get truecard from api
        $truecard = $this->checkTrueyouCard($cart->customer_ref_id);

        $discountItems = PromotionHelper::discountOnCartDetails($cart, $this->promotion);

        // true you that have same color card
        // cannot discount on same items

        // try to look in promotion data
        $promotionDatas = $cart->promotionData;
        foreach ($promotionDatas as $key => $promotionData) {
            // we will look at trueyou only
            if (array_get($promotionData, 'type') != 'trueyou')
            {
                continue;
            }

            // we will look at trueyou that have same color card
            if (array_get($promotionData, 'card') != $truecard)
            {
                continue;
            }

            // right now promotion is trueyou and have same color card
            $items = array_get($promotionData, 'discount.items');

            // get inventory from all cart detail
            $cartDetailsInventoryId = $cart->cartDetails->fetch('inventory_id')->toArray();

            // flip key and value because key is inventory id
            $promotionDataInventoryId = array_flip($items);

            $matches = array_intersect($cartDetailsInventoryId, $promotionDataInventoryId);

            // match!!!
            if (count($matches) > 0)
            {
                return false;
            }
        }

        if (count($discountItems))
        {
            $discount = array();
            $discount['cart'] = 0;
            $discount['items'] = $discountItems;

            // add promotion data
            $this->setPromotionDataToCart($cart, $truecard, $discount);
        }
    }

    public function disableTrueyou(Cart $cart)
    {
        if ($this->checkCouponCode($cart))
        {
            // cart has coupon code for sure so we set valid of true you be FALSE.
            $this->setValid(false);
            return true;
        }
        else
        {
            return false;
        }
    }

    protected function checkCouponCode($cart)
    {
        // check cart has promotion code or not?
        if (! $cart->promotionCode || ! is_array($cart->promotionCode))
        {
            return false;
        }

        // cart has promotion code so get coupon code with filter
        $filter = function($item)
        {
            return (@$item['data']['type'] == "coupon_code");
        };
        $filtered = array_filter($cart->promotionCode, $filter);

        // check filter should has some coupon code
        if (count($filtered) < 1)
        {
            return false;
        }

        return true;
    }

    protected function setPromotionDataToCart(Cart $cart, $truecard, $discount = null)
    {
        // get percent discount
        $promotionDiscount = array_get($this->promotion->effects, 'discount', array());
        $discountPercent = array_get($promotionDiscount, 'percent', '0');
        $discountBaht = array_get($promotionDiscount, 'baht', '0');
        $discountType = array_get($promotionDiscount, 'type', '0');

        $data = array(
            'id' => $this->promotion->getKey(),
            'type' => $this->promotion->promotion_category,
            'name' => $this->promotion->name,
            'description' => $this->promotion->description,
            'card' => $truecard,
            'discountPercent' => $discountPercent,
            'discountbaht' => $discountBaht,
            'discountType' => $discountType
        );

        if (is_array($discount) && count($discount))
        {
            $data['discount'] = $discount;
            $data['totalDiscount'] = $discount['cart'] + array_sum($discount['items']);
        }

        $cart->addPromotionData($data);
    }

    protected function checkTrueyouCard($ssoId)
    {
        $member = Member::where('sso_id', $ssoId)->first();

        // cannot fint member record
        if (! $member)
        {
            return false;
        }

        // get truecard from api
        return Cache::remember("truecard_status_{$member->thai_id}", 10, function() use ($member)
        {
            $truecard = App::make('truecard');
            $result = $truecard->getInfoByThaiId($member->thai_id)->check();

            $member->trueyou = $result ?: null;
            $member->save();

            return $result ?: false;
        });
    }

}