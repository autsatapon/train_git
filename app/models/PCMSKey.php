<?php

class PCMSKey extends Harvey {

    protected $table = 'pcms_keys';

    //protected $primaryKey = 'code';

    public static $rules = array(
        'code' => 'unique:pcms_keys,code',
    );

    public function verb()
    {
        return $this->belongsTo('Verb', 'vid');
    }

}