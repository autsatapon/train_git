<?php

class Member extends PCMSModel {

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    protected $appends = array('activated');

    /**
	 * The relationships that should be touched on save.
	 *
	 * @var array
	 */
	protected $touches = array();
   
    public static $autoKey = false;

    public function getActivatedAttribute()
    {
        return ($this->activated_at != false) ? true : false;
    }
}