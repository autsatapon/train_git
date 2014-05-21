<?php

class Translate extends Harvey {

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    public static $rules = array(
        'locale' => 'required',
        'languagable_id' => 'required',
        'languagable_type' => 'required',
        /* 'brandlogo' => 'image|max:2000', */
    );

    public function languagable()
    {
        return $this->morphTo();
    }

    public function files()
    {
        return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    }

}