<?php

class OrderXmlLog extends Eloquent {

    protected $table = 'order_xml_logs';

    public function order()
    {
        return $this->belongsTo('Order');
    }

}

