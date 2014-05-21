<?php

class Meta extends Eloquent {

    /**
     * Table app_metas.
     *
     * @var string
     */
    protected $table = 'app_metas';

    /**
     * Meta belongs to app.
     *
     * @return App
     */
    public function app()
    {
        return $this->belongsTo('PApp');
    }

    /**
     * Convert array to json before save options.
     *
     * @param  array $options
     * @return void
     */
    public function setOptionsAttribute($options)
    {
        $this->attributes['options'] = json_encode($options);
    }

    /**
     * Convert json to array before render.
     *
     * @param  string $options
     * @return object
     */
    public function getOptionsAttribute($options)
    {
        return json_decode($options);
    }

}