<?php namespace Promotion;

class Promotion {

    public static function factory($adapter)
    {
        $adapter = 'Promotion\\Adapter\\'.ucfirst($adapter);

        return new $adapter;
    }

}