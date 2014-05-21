<?php

class PApp extends PCMSModel {

    protected $table = 'apps';

    public static $rules = array(
        'name' => 'required',
        'url' => 'required',
        'foreground_url' => 'required|url',
        // 'accessible_ips' => 'required',
    );

    public static $labels = array(
        'name' => 'App Name',
        'url' => 'URL',
        'foreground_url' => 'Payment Foreground URL',
        'stock_code' => 'Stock Code',
        'nonstock_code' => 'None stock Code',
        'pkey' => 'App Key',
        'accessible_ips' => 'Accessible IPs',
        'free_shipping' => 'Free Shipping',
        'max_cc_per_user' => 'Max Credit Card Per User',
    );

    /*
    public static $rules = array(
        array('name', 'required'),
        array('url', 'required'),
        array('accessible_ips', 'required'),
    );
    */

    public static $currentApp = null;

    public static function boot()
    {
        parent::boot();

        static::saved(function($model)
        {
            Cache::forget('app-'.$model->pkey);
        });
    }

    public static function setCurrentApp($app)
    {
        static::$currentApp = $app;
    }

    public static function getCurrentApp()
    {
        return static::$currentApp;
    }

    public function note()
    {
        return $this->morphOne('Note', 'noteable');
    }

    public function shops()
    {
        return $this->hasMany('PAppShop', 'app_id')->select('name')->where('id',1);
    }

    public function users()
    {
        return $this->belongsToMany('User', 'user_app', 'app_id', 'user_id');
    }

    public function collections()
    {
        return $this->belongsToMany('Collection', 'apps_collections', 'app_id', 'collection_id');
    }

    public function metas()
    {
        return $this->hasMany('Meta', 'app_id');
    }

    public function getSlugAttribute($value)
    {
        return str_replace('-', '', Str::slug($this->attributes['name']));
    }

}