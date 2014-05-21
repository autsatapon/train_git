<?php

class ShippingController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Shipping', URL::to('shipping'));
    }

    public function getIndex()
    {
        echo 'shipping index.';
    }
}