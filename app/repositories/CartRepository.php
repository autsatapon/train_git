<?php

class CartRepository implements CartRepositoryInterface {

    protected $cart;
    protected $cartDetail;
    // protected $promotionRepository;

    public function __construct(Cart $cart = null, CartDetail $cartDetail = null)
    {
        if ($cart == false)
        {
            $this->cart = new Cart();
        }
        else
        {
            $this->cart = $cart;
        }

        if ($cartDetail == false)
        {
            $this->cartDetail = new CartDetail();
        }
        else
        {
            $this->cartDetail = $cartDetail;
        }

        // $this->promotionRepository = App::make("PromotionRepository");
    }

    public function find($id)
    {
        return $this->cart->findOrFail($id);
    }

    public function validCart($data)
    {
        $carts = $this->cart->where('app_id', '=', $data->app_id)->where('customer_ref_id', '=', $data->customer_ref_id)->first();
        return (is_null($carts)) ? false : true;
    }

    public function getCart($data)
    {
        if (!is_array($data))
        {
            $data = (array) $data;
        }

        if (empty($data['customer_type']) || empty($data['customer_ref_id']))
        {
            throw new Exception('customer_type and customer_ref_id are require.');
        }

        $cart = Cart::with(
                    array(
                        'cartDetails.vvendor',
                        'cartDetails.product.mediaContents',
                        'cartDetails.variant.mediaContents',
                        'cartDetails.variant.activeSpecialDiscount.discountCampaign',
                        'cartDetails.variant.translates',
                        'province',
                        'city',
                        'district',
                        'cartTrueyou'
                    )
                )
                ->where('app_id', $data['app_id'])
                ->where('customer_type', $data['customer_type'])
                ->where('customer_ref_id', $data['customer_ref_id'])
                ->first();

        if (empty($cart))
        {
            $this->createCart($data);
            return $this->getCart($data);
        }

        // reset discount and total_discount to 0
        $cart->discount = 0;

        $cart->cartDetails->each(function($item)
        {
            $item->total_discount = 0;
        });

        Event::fire('Cart.onGetCart', array($cart));

        $discountCampaignSetted = array();

        $cart->cartDetails->each(function($item) use ($cart, &$discountCampaignSetted)
        {
            $itemVisible = array('id', 'title', 'price', 'inventory_id', 'quantity', 'thumbnail', 'total_discount');

            // translate
            $translate = $item->variant->translates->filter(function($translate)
            {
                return $translate->locale == getRequestLocale();
            })->first();

            if ( ! is_null($translate))
            {
                $item->title = $translate->title;
            }

//            if ( ! isset($item->product->mediaContents))
//            {
//                $item->thumbnail = '';
//            }
//            else
//            {
//                $item->thumbnail = (string) $item->product->mediaContents->first()->thumbnail;
//            }

            $item->thumbnail = $item->image;

            // create discount campaign data
            if (
                ! empty($item->variant->activeSpecialDiscount)
                && ! empty($item->variant->activeSpecialDiscount->discountCampaign)
            ) {
                $discountCampaignId = $item->variant->activeSpecialDiscount->discountCampaign->id;
                if (! isset($discountCampaignSetted[$discountCampaignId]))
                {
                    $data = array(
                        'id' => $discountCampaignId,
                        'type' => $item->variant->activeSpecialDiscount->discountCampaign->type,
                        'name' => $item->variant->activeSpecialDiscount->discountCampaign->name,
                        'description' => $item->variant->activeSpecialDiscount->discountCampaign->description,
                        'code' => $item->variant->activeSpecialDiscount->discountCampaign->code,
                        'inventory_id' => array($item->variant->inventory_id)
                    );

                    $discountCampaignSetted[$discountCampaignId] = $data;
                }
                else
                {
                    $discountCampaignSetted[$discountCampaignId]['inventory_id'][] = $item->variant->inventory_id;
                }
            }

            // if ( ! empty($item->variant->active_trueyou_discount))
            // {
            //     $itemVisible[] = 'trueyou';
            //     $item->trueyou = $item->variant->active_trueyou_discount;

            //     if (isset($cart->cartTrueyou) && isset($item->trueyou[$cart->cartTrueyou->card]))
            //     {
            //         // $cartDetail = CartDetail::find($item->getKey());

            //         if ($item->trueyou[$cart->cartTrueyou->card]->discount_type == 'price')
            //         {
            //             $discount = $item->trueyou[$cart->cartTrueyou->card]->discount;
            //             $item->price = $item->price - $discount;
            //         }
            //         else // percent
            //         {
            //             $discount = $item->trueyou[$cart->cartTrueyou->card]->discount / 100 * $item->price;
            //             $item->price = $item->price - $discount;
            //         }

            //         $item->total_discount += $discount * $item->quantity;
            //     }
            // }

            $item->setVisible($itemVisible);
            $item->setAppends(array('totalPrice'));

            // save
            $cartDetail = CartDetail::find($item->getKey());
            $cartDetail->total_discount = $item->total_discount;
            $cartDetail->save();
        });

        // add discount campaign data
        foreach ($discountCampaignSetted as $key => $data) {
            $cart->addDiscountCampaignData($data);
        }

        $cartVisible = array('id', 'app_id', 'customer_ref_id', 'customer_email', 'cartDetails', 'discount', 'type');

        if ( ! is_null($cart->cartTrueyou))
        {
            $cart->cartTrueyou->setVisible(array('card', 'thai_id'));
            $cartVisible[] = 'cartTrueyou';
        }

        $cart->setVisible($cartVisible);
        $cart->setAppends(array('totalPrice', 'totalItem', 'totalQty', 'promotionCode', 'promotionData', 'cashVoucher', 'discountCampaignData'));

        $cart->total = $cart->total_price;
        $cart->save();

        return $cart;
    }

    public function createCart($data)
    {
        $cart = new Cart;

        $cart->app_id = $data['app_id'];
        $cart->customer_type = $data['customer_type'];
        $cart->customer_ref_id = $data['customer_ref_id'];

        if (! $cart->save())
        {
            throw new Exception("Can't create cart.");
        }

        return $cart;
    }

    public function addItem($data)
    {
        $defaultData = array(
            'type' => 'normal'
        );

        $data = array_merge($defaultData, $data);

        $variant = ProductVariant::with(array('activeSpecialDiscount'))->where('inventory_id', $data['inventory_id'])->first();
        //$variant = ProductVariant::find(1);
        // if invalid $data['inventory_id']
        if (!$variant)
        {
            // return FALSE;
            throw new Exception('No variant.');
        }

        // Check Remaining before Add to Cart
        $stockRepo = new StockRepository;
        $stockRemaining = $stockRepo->checkRemainings($data['app_id'], $data['inventory_id']);

        $cart = $this->getCart($data);

        if (empty($cart))
        {
            $cart = $this->createCart($data);
        }

        $cartDetail = $cart->cartDetails()->where('variant_id', $variant->id)->first();

        // bottom line:
        // - you can not have installment item and normal item in same cart
        // - installment item only comply by 1
        //
        // if cart is empty,
        // you can put items what you want but if item is installment, quantity must be 1
        // if cart is not empty,
        // only type's cart is normal and adding item is not an installment
        if (
            empty($cartDetail) ||
            ( ! empty($cartDetail) && $cart->type == 'normal' && $data['type'] != 'installment')
           )
        {
            // installmment only comply by 1
            if ($data['type'] == 'installment')
            {
                $data['qty'] = 1;
            }

            if (empty($cartDetail))
            {
                if ($stockRemaining[$data['inventory_id']] < $data['qty'])
                {
                    throw new Exception('No Stock Remaining. (new item in cart), inventory_id '.$data['inventory_id'].' remaining '.$stockRemaining[$data['inventory_id']].', try to add '.$data['qty']);
                }

                $cartDetail = new CartDetail();
                $cartDetail->cart_id = $cart->id;
                $cartDetail->inventory_id = $variant->inventory_id;
                $cartDetail->product_id = $variant->product_id;
                $cartDetail->variant_id = $variant->id;
                $cartDetail->title = $variant->title;
                $cartDetail->vendor_id = $variant->vendor_id;
                $cartDetail->shop_id = $variant->shop_id;

                $cartDetail->price = $variant->price;
                $cartDetail->quantity = $data['qty'];

                $cartDetail->save();
                $cart->cartDetails()->save($cartDetail);
            }
            else
            {
                $cartDetail->quantity += $data['qty'];

                if ($stockRemaining[$data['inventory_id']] < $cartDetail->quantity)
                {
                    throw new Exception('No Stock Remaining. (exists item in cart), inventory_id '.$data['inventory_id'].' remaining: '.$stockRemaining[$data['inventory_id']].', try to add '.$cartDetail->quantity);
                }

                $cartDetail->save();
            }

            $cart->type = ($data['type']=='')?'normal':$data['type'];

            Event::fire('Cart.setUnConfirm', array($cart));

            $cart->save();
        }
        else
        {
            throw new Exception('Error, cannot add to cart (cart record has problem)');
        }

        $cart = $this->getCart($data);
        // $cart->total += $variant->price * $data['qty'];
        // $cart->total = $cart->total_price;
        // $cart->discount = $this->calculateDiscount();

        // $cart->save();

        return $cart;
    }

    public function removeItem($data)
    {
        $variant = ProductVariant::with(array('activeSpecialDiscount'))->where('inventory_id', $data['inventory_id'])->first();

        // if invalid $data['inventory_id']
        if (!$variant)
        {
            return FALSE;
        }

        $cart = $this->getCart($data);



        if (empty($cart))
        {
            return FALSE;
        }

        $cartDetail = $cart->cartDetails()->where('variant_id', $variant->id)->first();

        if (!empty($cartDetail))
        {
            // $totalPrice = $cartDetail->totalPrice;
            $cartDetail->delete();
            $cart = $this->getCart($data);

            Event::fire('Cart.setUnConfirm', array($cart));

            // $cart->total -= $totalPrice;
            // $cart->total = $cart->total_price;
            // $cart->discount = $this->calculateDiscount();
            // $cart->save();
        }

        /*
          // ถ้าลบซะจน Cart Item Empty ล่ะ ???
          if ($cart->cartDetails->isEmpty())
          {

          }
         */

        return $cart;
    }

    public function updateItem($data)
    {
        $cart = $this->getCart($data);

        if (empty($cart))
        {
            throw new Exception('Cart not found');
        }

        foreach ($data['items'] as $item)
        {
            $variant = ProductVariant::with(array('activeSpecialDiscount'))->where('inventory_id', $item['inventory_id'])->first();

            if (empty($variant))
            {
                throw new Exception('Variant '.$item['inventory_id'].' not found');
            }

            $cartDetail = $cart->cartDetails()->where('variant_id', $variant->getKey())->first();

            if ( ! empty($cartDetail))
            {
                /* =============================================================== */
                // Check Remaining before Update item
                $stockRepo = new StockRepository;
                $stockRemaining = $stockRepo->checkRemainings($data['app_id'], $item['inventory_id']);

                if ($stockRemaining[$item['inventory_id']] < $item['qty'])
                {
                    return array('status' => false, 'message' => 'Stock not enough', 'variant' => $variant->toArray(), 'qty' => $item['qty'], 'remaining' => $stockRemaining[$item['inventory_id']]);
//                    throw new Exception('Stock not enough');
                }
                /* =============================================================== */

                $cartDetail->quantity = $item['qty'];
                $cartDetail->save();
            }
        }

        $cart = $this->getCart($data);

        Event::fire('Cart.setUnConfirm', array($cart));

        // $cart->total = $cart->total_price;
        // $cart->discount = $this->calculateDiscount();
        // $cart->save();

        return $cart;
    }

    public function updateCustomer($data)
    {
        $nonUserData = array(
            'customer_type' => Cart::NON_USER,
            'customer_ref_id' => $data['customer_ref_id'],
        ) + $data;

        $userData = array(
            'customer_type' => Cart::USER,
            'customer_ref_id' => $data['updated_ref_id'],
        ) + $data;

        $nonUserCart = $this->getCart($nonUserData);
        $userCart = $this->getCart($userData);

        if (empty($nonUserCart))
        {
            return FALSE;
        }

        if (count($nonUserCart->cartDetails) == 0)
        {
            $nonUserCart->forceDelete();
            return FALSE;
        }

        // merge cart
        if (! empty($userCart))
        {
            $userCartId = $userCart->getKey();

            $nonUserItems = $nonUserCart->cartDetails->lists('quantity', 'inventory_id');
            $userItems = $userCart->cartDetails->lists('quantity', 'inventory_id');

            // ถ้ามีสินค้าชนิดเดียวกันให้เอาของ ตะกร้า guest เป็นหลัก
            $inventoryIds = array_keys($nonUserItems);
            if (count($inventoryIds) > 0)
            {
                $userCart->cartDetails->map(function($cartDetail) use ($nonUserItems, $inventoryIds) {
                    if (in_array($cartDetail->inventory_id, $inventoryIds))
                    {
                        // d($cartDetail->toArray());
                        $cartDetail->quantity = $nonUserItems[$cartDetail->inventory_id];
                        $cartDetail->__unset('thumbnail');
                        $cartDetail->save();
                    }
                });
            }

            // รวมสินค้าในตะกร้า guest เข้า ตะกร้าเดิมของ user
            $diffItems = array_keys(array_diff_key($nonUserItems, $userItems));
            $nonUserCart->cartDetails->map(function($cartDetail) use ($diffItems, $userCartId)
            {
                if (in_array($cartDetail->inventory_id, $diffItems))
                {
                    // d($cartDetail->toArray());
                    $cartDetail->cart_id = $userCartId;
                    $cartDetail->__unset('thumbnail');
                    $cartDetail->save();
                }
                else
                {
                    // d($cartDetail->toArray());
                    $cartDetail->forceDelete();
                }
            });
        }

        $nonUserCart->forceDelete();
        $userCart->save();

        return TRUE;
    }

    public function deleteCart($data)
    {
        $cart = $this->getCart($data);

        if (empty($cart))
        {
            return;
        }

        $cart->cartDetails()->delete();
        $cart->delete();

        return;
    }

    /*
      public function createCart($data)
      {
      $variant = ProductVariant::find($data->item);

      if (! $variant)
      {
      return false;
      }

      $carts = $this->cart->where('app_id', '=', $data->app_id)->where('customer_ref_id', '=', $data->customer_ref_id)->first();

      if (is_null($carts)) {
      $carts = new Cart();
      }

      $carts->app_id = $data->app_id ;
      $carts->customer_ref_id = $data->customer_ref_id ;
      $carts->save() ;


      $cartDetail = $this->cartDetail->where('inventory_id','=',$data->item)->where('cart_id','=',$carts->id)->first();

      if (is_null($cartDetail)) {

      $cartDetail = new CartDetail();

      $cartDetail->cart_id = $carts->id ;
      $cartDetail->variant_id = $variant->variant_id ;
      $cartDetail->inventory_id = $data->item;
      $cartDetail->product_id = $variant->product_id;
      $cartDetail->title = $variant->title;
      $cartDetail->price = $variant->price;
      $cartDetail->vendor_id = $variant->vendor_id;
      $cartDetail->shop_id = $variant->shop_id;

      }

      $cartDetail->quantity = $cartDetail->quantity + $data->qty;
      $cartDetail->save();

      // calculate discount
      $carts->discount = $this->calculateDiscount();
      $carts->save() ;


      return  $cartDetail;
      }

      public function getCart($data)
      {
      if ($this->validCart($data)) {

      $cart = $this->cart->with(array('cartDetails.variant'))->where('app_id', '=', $data->app_id)->where('customer_ref_id', '=', $data->customer_ref_id)->first();
      $cartDetails = $cart->cartDetails;

      $cart->cartDetails->each(function($item) {
      $item->setVisible(array('id', 'title', 'price', 'quantity'));
      $item->setAppends(array('totalPrice'));
      });

      $cart->setVisible(array('id', 'app_id', 'customer_ref_id', 'cartDetails', 'discount'));
      // $cart->setAppends(array('totalPrice', 'item', 'totalItem'));
      $cart->setAppends(array('totalPrice', 'totalItem', 'totalQty'));

      return $cart;
      }

      return;

      }

      public function updateCart($data)
      {
      return $this->createCart($data);
      }

      public function deleteCart($data)
      {

      if ($this->validCart($data)) {

      $carts = $this->cart->where('app_id', '=', $data->app_id)->where('customer_ref_id', '=', $data->customer_ref_id)->first();
      $cartDetail = $this->cartDetail->where('inventory_id','=',$data->item)->where('cart_id','=',$carts->id);

      // $queries = DB::getQueryLog();
      // $last_query = end($queries);

      return $cartDetail->delete() ;
      }
      }
     */

    // public function checkTrueyou($data)
    // {
    //     $cart = $this->getCart($data);

    //     if (! $cart->cartTrueyou)
    //     {
    //         $cart->load('cartTrueyou');
    //     }

    //     return (boolean) $cart->cartTrueyou;
    // }

    public function applyTrueyou($data)
    {
        if ( ! isset($data['thai_id']))
        {
            $member = Member::whereAppId($data['app_id'])->whereSsoId($data['customer_ref_id'])->first();

            if ($member)
            {
                $data['thai_id'] = $member['thai_id'];
            }
            else
            {
                return false;
                // throw new Exception('Member not found with sso_id('.$data['customer_ref_id'].') and app_id('.$data['app_id'].')');
            }
        }

        // $trueCard = App::make('truecard');
        // $result = $trueCard->getInfoByThaiId($data['thai_id'])->check();

        $this->cart = $this->getCart($data);

        // if (is_null($cart->cartTrueyou))
        // {
        //     // insert
        //     $cartTrueyou = new CartTrueyou;
        //     $cartTrueyou->cart_id = $cart->getKey();
        // }
        // else
        // {
        //     // update
        //     $cartTrueyou = $cart->cartTrueyou;
        // }

        // $cartTrueyou->card = $result?:null;
        // $cartTrueyou->thai_id = $data['thai_id'];
        // $cartTrueyou->expired_at = date('Y-m-d H:i:s', strtotime('+ 10 minutes'));
        // $cartTrueyou->save();

        Event::fire('Cart.onApplyTrueyou', array($this->cart));

        // $this->calculateTrueyou($cart);
    }

    public function removeTrueyou($data)
    {
        $this->cart = $this->getCart($data);

        Event::fire('Cart.onRemoveTrueyou', array($this->cart));

        // if ( ! is_null($cart->cartTrueyou))
        // {
        //     $cart->cartTrueyou->delete();
        // }

        // $this->calculateTrueyou($cart);
    }

    public function reApplyTrueyou($data)
    {
        $this->cart = $this->getCart($data);

        Event::fire('Cart.onApplyTrueyou', array($this->cart));

        // if ( ! is_null($cart->cartTrueyou))
        // {
        //     // already expired, re-check
        //     if ($cart->cartTrueyou->expired_at < date('Y-m-d H:i:s'))
        //     {
        //         $trueCard = App::make('truecard');
        //         $result = $trueCard->getInfoByThaiId($cart->cartTrueyou->thai_id)->check();

        //         $cart->cartTrueyou->card = $result?:null;
        //         $cart->cartTrueyou->expired_at = date('Y-m-d H:i:s', strtotime('+ 10 minutes'));
        //         $cart->cartTrueyou->save();

        //         $this->calculateTrueyou($cart);
        //     }
        // }
    }

    // public function calculateTrueyou(Cart $cart)
    // {
    //    if ( ! is_null($cart->cartTrueyou) && ! empty($cart->cartDetails))
    //    {
    //        foreach ($cart->cartDetails as $item)
    //        {
    //            $cart->cartDetails->product->variants->active_trueyou_discount
    //        }
    //    }
    // }

    public function applyCoupon($data, $coupon)
    {
        $this->cart = $this->getCart($data);

        if ($this->cart->type == 'installment')
        {
            return false;
        }

        $this->cart->setPromotionCode();
        $this->cart->setPromotionData();

        $coupon = strtoupper($coupon);

        // $promotionCode = PromotionCode::with('promotion.campaign')
        //             ->whereCode($coupon)
        //             ->orderBy('created_at', 'desc')
        //             ->first();

        // // dont' have promotion code
        // if (! $promotionCode)
        // {
        //     return array('errorCode' => '4102', 'errorMessage' => 'Code is invalid.');
        //     // return 'โค้ดนี้ไม่ถูกต้อง';
        // }

        // if (! $promotionCode->checkValidCode())
        // {
        //     return array('errorCode' => '4111', 'errorMessage' => 'Code is already used.');
        // }

        // // if promotion code is useable on this cart
        // $discountCart = \Promotions\PromotionHelper::discountOnCart($this->cart, $promotionCode->promotion);
        // $discountItems = \Promotions\PromotionHelper::discountOnCartDetails($this->cart, $promotionCode->promotion);
        // if (
        //     ! (
        //         (
        //             ($discountCart == 0 && count($discountItems) != 0)
        //             || $discountCart != 0
        //         )
        //     )
        // ) {
        //     return array('errorCode' => '4112', 'errorMessage' => 'No item in cart is effected. Please add valid item.');
        // }

        // if (
        //     ! (
        //         $promotionCode->promotion
        //         && $promotionCode->promotion->checkActive()
        //         && $promotionCode->promotion->campaign
        //         && $promotionCode->promotion->campaign->checkActive()
        //     )
        // ) {
        //     // promotion code is not useable
        //     return array('errorCode' => '4101', 'errorMessage' => 'Code is expired.');
        //     // return 'โค้ดนี้หมดอายุ';
        // }

        $result = Event::fire('Cart.onApplyCode', array($this->cart, $coupon));
        $result = array_filter($result);

        // count result more than 0 so this should has error
        if (count($result) > 0)
        {
            foreach($result as $res)
            {
                if (is_array($res))
                {
                    return $res;
                }
            }
        }
        else
        {
            // no results so check this code is exists in system or not
            $promotionCode = PromotionCode::whereCode($coupon)
                            ->orderBy('created_at', 'desc')
                            ->first();

            if ($promotionCode)
            {
                return array('errorCode' => '4101', 'errorMessage' => 'Code is expired.');
                // return 'โค้ดนี้หมดอายุ';
            }
            else
            {
                return array('errorCode' => '4102', 'errorMessage' => 'Code is invalid.');
                // return 'โค้ดนี้ไม่ถูกต้อง';
            }
        }

        $this->cart = $this->getCart($data);

        return $this->cart;
    }

    public function removeCoupon($data, $coupon)
    {
        $this->cart = $this->getCart($data);

        Event::fire('Cart.onDeapplyCode', array($this->cart, $coupon));

        $this->cart = $this->getCart($data);

        Event::fire('Cart.onApplyTrueyou', array($this->cart));

        $this->cart = $this->getCart($data);

        return $this->cart;
    }

    
    public function applyEmail($data, $method = "delete")
    {
        $this->cart = $this->getCart($data);
        
        if(strtolower($method) == 'post' && !empty($data['customer_email'])){
            $this->cart->customer_email = $data['customer_email'];
        }else{
            $this->cart->customer_email = "";
        }
        $this->cart->save();

        return $this->cart;
    }
}
