<?php namespace Promotions;

use Cart;
use CartDetail;
use Closure;
use Promotion as PromotionModel;
use Illuminate\Database\Eloquent\Collection;

class PromotionHelper {

    public static function calculateDiscountPrice($price, $discount)
    {
        return (int) floor(min($discount, $price));
    }

    public static function calculateDiscountPercent($price, $discountPercentage)
    {
        return (int) floor($price * (min($discountPercentage, 100) / 100));
    }

    /**
     * get parent pkey of cart details from cart
     * @param  Cart   $cart
     * @return array
     */
    public static function getVariantPKeyFromCart(Cart $cart)
    {
        // load all variants
        if (
            ! $cart->cartDetails
            || ! $cart->cartDetails->first()
            || ! $cart->cartDetails->first()->variant
        ) {
            $cart->load('cartDetails.variant');
        }

        // create variant collections
        $variants = new Collection;
        foreach($cart->cartDetails as $cartDetail)
        {
            $variants->add($cartDetail->variant);
        }

        // get pkey of all variants
        return \PKeysRepository::prepare($variants, 'parent')->get();
    }

    /**
     * discount on cart
     * @param  Cart      $cart
     * @param  Promotion $promotion
     * @return Cart
     */
    public static function discountOnCart(Cart $cart, PromotionModel $promotion)
    {
        $totalDiscount = 0;

        $discount = array_get($promotion->effects, 'discount', array());
        if (! $discount)
        {
            return $cart;
        }

        // cart discount อย่าให้ไปดึงจาก db
        if (! $cart->discount)
        {
            $cart->discount = 0;
        }

        // check discount on cart
        if (array_get($discount, 'on') == 'cart')
        {
            if (array_get($discount, 'type') == 'percent')
            {
                // discount all cart by percent
                $cartDiscount = static::calculateDiscountPercent($cart->totalPrice, $discount['percent']);
                $cart->discount += $cartDiscount;
                $totalDiscount += $cartDiscount;
            }

            if (array_get($discount, 'type') == 'price')
            {
                // discount all cart by price
                $cartDiscount = static::calculateDiscountPrice($cart->totalPrice, $discount['baht']);
                $cart->discount += $cartDiscount;
                $totalDiscount += $cartDiscount;
            }
        }

        return $totalDiscount;
    }

    /**
     * discount on cartDetails
     * @param  Cart      $cart
     * @param  Promotion $promotion
     * @return Cart
     */
    public static function discountOnCartDetails(Cart $cart, PromotionModel $promotion)
    {
        $discount = array_get($promotion->effects, 'discount', array());
        if (! $discount)
        {
            return $cart;
        }

        $totalDiscount = array();

        $action = function(CartDetail $cartDetail) use ($discount, &$totalDiscount)
        {
            if (! $cartDetail->total_discount)
            {
                $cartDetail->total_discount = 0;
            }

            // match so we will discount this cart item
            if (array_get($discount, 'type') == 'percent')
            {
                $itemDiscount = PromotionHelper::calculateDiscountPercent($cartDetail->price, array_get($discount, 'percent'));
            }

            if (array_get($discount, 'type') == 'price')
            {
                $itemDiscount = PromotionHelper::calculateDiscountPrice($cartDetail->price, array_get($discount, 'baht'));
            }

            if (! empty($itemDiscount))
            {
                $itemDiscount = $cartDetail->quantity * $itemDiscount;
                $cartDetail->total_discount += $itemDiscount;
                $totalDiscount[$cartDetail->inventory_id] = $itemDiscount;
            }

        };

        self::walkOnFollowingItems($cart, $discount, $action);

        return $totalDiscount;
    }

    protected static function walkOnFollowingItems(Cart $cart, Array $promotionEffect, Closure $action)
    {
        // check promotion effect on following items
        if (array_get($promotionEffect, 'on') == 'following')
        {
            // get pkey of all variants
            $pkeyCollection = static::getVariantPKeyFromCart($cart);

            // get pkey from discount
            $pkeyFromPromotionEffect = array_get($promotionEffect, 'following_items', array());

            // create exclude list
            $excludeList = array_merge(
                    explodeFilter(',', array_get($promotionEffect, 'exclude_product.un_following_items')),
                    explodeFilter(',', array_get($promotionEffect, 'exclude_variant.un_following_items'))
                    );

            // loop for check each cart items
            foreach ($cart->cartDetails as $key => $cartDetail) {
                // try to get pkeys from collection
                // if empty so skip it.
                if (empty($pkeyCollection[$cartDetail->variant_id]))
                {
                    continue;
                }

                // found pkey
                $pkeyFromRepo = $pkeyCollection[$cartDetail->variant_id];

                // get match from exclude list
                $excludeMatches = array_intersect($pkeyFromRepo, $excludeList);

                // match exclude so go at next variant
                if (count($excludeMatches) > 0)
                {
                    continue;
                }

                // get matches
                $matches = array_intersect($pkeyFromRepo, $pkeyFromPromotionEffect);

                // if it not matches
                if (count($matches) < 1)
                {
                    continue;
                }

                // action!!!
                $action($cartDetail);
            }
        }

        return $cart;
    }

}