<?php

class Tag extends Harvey {

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    public static $rules = array(
         array('detail', 'required|unique:tags,detail')
    );

} 