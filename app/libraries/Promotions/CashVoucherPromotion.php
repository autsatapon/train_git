<?php namespace Promotions;

use Cart;

class CashVoucherPromotion extends BaseCodePromotion {

    protected $eventPriority = 10;

    protected $promotionCodeType = "cash_voucher";

    public function isActive()
    {
        return true;
    }

    public function applyDiscount(Cart $cart)
    {
        if ($cart->type == 'installment')
        {
            $this->setValid(false);
            return false;
        }

        $discount = array_get($this->promotion->effects, 'discount', array());

        $cashVoucher = 0;
        if (array_get($discount, 'type') == 'price')
        {
            // discount all cart by price
            $cashVoucher = max(intval(array_get($discount, 'baht')), 0);

            // 1 cart can use only 1 cash voucher
            $cart->setCashVoucher($cashVoucher);
        }

        $discount = array();
        $discount['cart'] = 0;
        $discount['items'] = array();

        $this->setPromotionDataToCart($cart, $discount, $cashVoucher);
    }

}