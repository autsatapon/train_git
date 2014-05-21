<?php namespace Enhanced\Facades;

use Illuminate\Support\Facades\Facade as LaraveFacade;

class Abc extends LaraveFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'AbcGenerator'; }

}