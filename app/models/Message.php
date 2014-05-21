<?php

class Message extends Harvey {

    public static $rules = array(
        'messageable_id' => 'required',
        'messageable_type' => 'required',
    );

    public function messagable()
    {
        return $this->morphTo();
    }

    public function scopeIsQueue($query)
    {
        return $query->whereStatus('queue');
    }

    public function send()
    {

    }

}

