<?php

class ProductStyleOption extends PCMSModel {

    public static $autoKey = false;

    protected $with = array('styleOption');

    // public $timestamps = false;

    // protected $softDelete = false;


    public function product()
    {
        return $this->belongsTo('Product');
    }

    public function styleOption()
    {
        return $this->belongsTo('StyleOption');
    }

    /**
     * reletion to media_content
     */
    public function mediaContents()
    {
        return $this->morphMany('MediaContent', 'mediable');
    }

    public function getTextAttribute($value)
    {
        return $value ?: $this->styleOption->text;
    }

    public function getMetaAttribute($value)
    {
        return $value ?: $this->styleOption->meta;
    }

    public function getImageAttribute()
    {
        if (! isset($this->attributes['meta']))
        {
            $this->attributes['meta'] = '[]';
        }

        if (is_array($this->attributes['meta']))
        {
            $meta = $this->attributes['meta'];
        }
        else
        {
            $meta = json_decode($this->attributes['meta'], true);
        }

        if (array_get($meta, 'type') == 'image')
        {
            $path = array_get($meta, 'value');

            return URL::isValidUrl($path) ? $path : Config::get('up::uploader.baseUrl').'/'.$path;
        }

        return null;
    }
}