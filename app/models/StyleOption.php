<?php

class StyleOption extends PCMSModel {

    protected $softDelete = false;

    public $timestamps = false;

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