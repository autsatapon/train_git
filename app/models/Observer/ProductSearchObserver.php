<?php namespace Observer;

class ProductSearchObserver {

    public function updated($model)
    {
        \ElasticUtils::updateProduct($model);
    }

    // public function deleted($model)
    public function deleting($model)
    {
        \ElasticUtils::removeProduct($model);
    }
}