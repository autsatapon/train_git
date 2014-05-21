<?php

class OrderReconcileLog extends Eloquent {
    
    protected $table = 'order_reconcile_logs';

    public function order()
    {
        return $this->belongsTo('Order');
    }
    
}