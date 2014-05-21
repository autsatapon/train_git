<?php namespace Enhanced;

use Illuminate\Routing\UrlGenerator as LaravelUrlGenerator;

class UrlGenerator extends LaravelUrlGenerator {

    public function hello()
    {
        return "Hello";
    }

}