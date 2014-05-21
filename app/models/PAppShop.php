<?php

class PAppShop extends PCMSModel {

    protected $table = 'app_shops';

    public static $rules = array(
        'app_id' => 'required',
        'code' => 'required',
        'name' => 'required'
    );

    public function note()
    {
        return $this->morphOne('Note', 'noteable');
    }

    public function app()
    {
        return $this->belongsTo('PApp', 'app_id');
    }

}