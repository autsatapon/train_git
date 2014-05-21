<?php

class PolicyRelate extends PCMSModel {

    public static $autoKey = false;

    protected $with = array('policy', 'translates');

    public function policy()
    {
        return $this->belongsTo('Policy');
    }

    public function policiable()
    {
        return $this->morphTo();
    }

    public function getTitleAttribute($value)
    {
        if ($this->use_type == 'yes')
        {
            return $this->policy->title;
        }

        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if ($this->use_type == 'yes')
        {
            return $this->policy->description;
        }

        return $value;
    }

    public function getTypeAttribute($value)
    {
        if ($this->use_type == 'yes')
        {
            return $this->policy->type;
        }

        return $value;
    }

    public function getTranslatesAttribute()
    {
        if ($this->use_type == 'yes')
        {
            return $this->policy->translates;
        }

        return $this->relations['translates'];
    }

}