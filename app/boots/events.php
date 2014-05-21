<?php

Event::listen('Cart.setUnConfirm', function(Cart $cart)
{
    $cart->confirm_checkout = 0;

    $cart->save();
});