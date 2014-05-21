<?php

class Logger extends Eloquent {

    protected $guarded = array();

    public function setContextAttribute($context)
    {
        if (is_array($context))
        {
            $this->attributes['context'] = json_encode($context);
        }
    }

    public function getContextAttribute($context)
    {
        return json_decode($context, true);
    }

}