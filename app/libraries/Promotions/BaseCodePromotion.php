<?php namespace Promotions;

use Cart;
use Order;
use PromotionCode;
use PromotionCodeLog;

abstract class BaseCodePromotion extends Promotion {

    protected $eventPriority = null;

    protected $promotionCodeType = null;

    public function isActive()
    {
        return true;
    }

    public function register()
    {
        $this->bindValidator('Cart.onApplyCode', 'applyCode');
        $this->bindApplyIfValid('Checkout.onCreatingOrder', 'checkCode');
        $this->bindValidator('Cart.onDeapplyCode', 'removeCode');
    }

    public function applyEffect()
    {
        $this->bindApplyIfValid('Cart.onApplyTrueyou', 'showPromotionCode');
        $this->bindApplyIfValid('Cart.onGetCart', 'applyDiscount');
        $this->bindApplyIfValid('Checkout.onCreatedOrder', 'useCodes');
        // $this->bindApplyIfValid('Order.onCancelOrder', 'recoverCodes');
    }

    public function useCodes(Order $order)
    {
        $promotionCode = $this->getCurrentPromotionCode();

        if ($promotionCode)
        {
            $promotionCode->useCode();

            // double check promotion code log
            // but it shouldn't exists
            $promotionCodeLog = $promotionCode->promotionCodeLogs()
                                ->whereOrderId($order->getKey())
                                ->first();

            if (! $promotionCodeLog)
            {
                // save promotion code log
                $promotionCodeLog = new PromotionCodeLog;
                $promotionCodeLog->order_id = $order->getKey();
                $promotionCode->promotionCodeLogs()->save($promotionCodeLog);
            }

            return true;
        }

        return null;
    }

    // public function recoverCodes(Order $order)
    // {
    //     $metaData = $this->getMetaData();
    //     $promotionCodeId = array_get($metaData, 'data.id');
    //     if (! $promotionCode)
    //     {
    //         return null;
    //     }

    //     $promotionCode = PromotionCode::find($promotionCodeId);

    //     // $promotionCode = $this->getCurrentPromotionCode();

    //     if ($promotionCode)
    //     {
    //         $this->setValid(false);
    //         $promotionCode->recoverCode();
    //     }
    // }

    public function checkCode(Cart $cart)
    {
        // check promotion code that expired
        if (! $this->getCurrentPromotionCode())
        {
            // d($this);
            // $this->setValid(false);
            return 'promotion_code_expired';
        }

        return true;
    }

    private function getCurrentPromotionCode()
    {
        $metaData = $this->getMetaData();
        $promotionCode = array_get($metaData, 'data.code');
        if (! $promotionCode)
        {
            return false;
        }

        $promotionCode = PromotionCode::wherePromotionId($this->promotion->getKey())
                            ->validCode($promotionCode)->first();

        if (! $promotionCode)
        {
            return false;
        }

        return $promotionCode;
    }

    public function applyCode(Cart $cart, $code)
    {
        $code = strtoupper($code);

        if ($cart->type == 'installment')
        {
            // set valid false
            return false;
        }

        // try to get current code that applied from this promotion
        $currentCode = array_get($this->getMetaData(), 'data.code');

        // so has it and user want reapply same code
        if ($currentCode)
        {
            if (strtoupper($currentCode) == $code)
            {
                $this->setPromotionDataToCart($cart);
                return true;
            }
        }

        $promotionCode = PromotionCode::with('promotion.campaign')
                            ->whereCode($code)
                            ->whereType($this->promotionCodeType)
                            ->orderBy('created_at', 'desc')
                            // ->remember(1)
                            ->first();

        // not found promotion under this promotion
        // we will return null because this should not return false,
        // it can make another code that added before will be false
        if (! $promotionCode)
        {
            return null;
        }

        // so we found promotion code

        // promotion id not match
        // so I will reject applied code because each promotion code type can apply single code only
        if ($promotionCode->promotion_id != $this->promotion->id)
        {
            // if promotion code is useable
            $discountCart = PromotionHelper::discountOnCart($cart, $promotionCode->promotion);
            $discountItems = PromotionHelper::discountOnCartDetails($cart, $promotionCode->promotion);
            if (
                $promotionCode->checkValidCode()
                && (
                    ($discountCart == 0 && count($discountItems) != 0)
                    || $discountCart != 0
                    )
                && $promotionCode->promotion
                && $promotionCode->promotion->checkActive()
                && $promotionCode->promotion->campaign
                && $promotionCode->promotion->campaign->checkActive()
            ) {
                // kick this promotion
                return false;
            }
            else
            {
                // promotion code cannot use so don't kick this promotion
                return null;
            }

        }

        // check promotion code is valid or not
        if (! $promotionCode->checkValidCode())
        {
            $this->setValid(false);
            return array('errorCode' => '4111', 'errorMessage' => 'Code is already used.');
        }


        $discountCart = PromotionHelper::discountOnCart($cart, $this->promotion);

        if ($discountCart == 0)
        {
            $discountItems = PromotionHelper::discountOnCartDetails($cart, $this->promotion);

            if (count($discountItems) == 0)
            {
                $this->setValid(false);
                return array('errorCode' => '4112', 'errorMessage' => 'No item in cart is effected. Please add valid item.');
            }
        }

        // ผูก coupon กับ cart ไว้ใน event app after
        $this->setMetaData(array(
            'type' => 'promotion_code',
            'data' => array(
                'type' => $promotionCode->type,
                'id'   => $promotionCode->getKey(),
                'code' => $promotionCode->code
            )
        ));

        $this->setPromotionDataToCart($cart);

        // set valid true
        return true;
    }

    public function showPromotionCode(Cart $cart)
    {
        $this->setPromotionDataToCart($cart);
    }

    public function removeCode(Cart $cart, $code)
    {
        // ดูว่า coupon code ที่จะเอาออกอยู่ใน cart รึเปล่า
        $metaData = $this->getMetaData();
        if (strtoupper(array_get($metaData, 'data.code')) == strtoupper($code))
        {
            //ถ้าอยู่ก็เอาออก และ setValid(false);
            $this->setMetaData(array());
            $this->setValid(false);
            return false;
        }

        return null;
    }

    public function applyDiscount(Cart $cart)
    {
        if ($cart->type == 'installment')
        {
            $this->setValid(false);
            return false;
        }

        $discountCart = PromotionHelper::discountOnCart($cart, $this->promotion);
        $discountItems = PromotionHelper::discountOnCartDetails($cart, $this->promotion);

        if (count($discountCart) || count($discountItems))
        {
            $discount = array();
            $discount['cart'] = $discountCart;
            $discount['items'] = $discountItems;

            $this->setPromotionDataToCart($cart, $discount);
        }
    }

    protected function setPromotionDataToCart(Cart $cart, $discount = null, $cashVoucher = 0)
    {
        $cart->addPromotionCode($this->getMetaData());
        $data = array(
            'id' => $this->promotion->getKey(),
            'type' => $this->promotion->promotion_category,
            'name' => $this->promotion->name,
            'description' => $this->promotion->description,
            'code' => array_get($this->getMetaData(), 'data.code'),
        );

        if (is_array($discount) && count($discount))
        {
            $data['discount'] = $discount;
        }

        if ($cashVoucher)
        {
            $data['cashVoucher'] = $cashVoucher;
        }

        $data['totalDiscount'] = array_get($discount, 'cart', 0)
                                 + array_sum(array_get($discount, 'items', array()))
                                 + max($cashVoucher, 0);

        $cart->addPromotionData($data);
    }
}