<?php

App::singleton('truecard', function()
{
    $config = Config::get('endpoints.truecard');

    return new TrueCard\TrueCard($config);
});

App::bind('PCMSPromotionCart', function(){

    $route = Route::getCurrentRoute();

    $app = $route->getParameter('pkey');

    // get cart
    $data = array();
    $data['customer_ref_id'] = Input::get('customer_ref_id', Request::segment(5));
    $data['customer_type'] = Input::get('customer_type', Request::segment(6));
    $data['app_id'] = $app->id;
    $cartRepo = new CartRepository;
    $cart = $cartRepo->getCart($data);

    if ($cart)
    {
        // run promotion
        return PCMSPromotion::run($cart);
    }
});