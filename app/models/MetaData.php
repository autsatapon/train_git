<?php

class MetaData extends Eloquent {

    protected $table = 'app_metadatas';

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    public function metadatable()
    {
        return $this->morphTo();
    }

    public function app()
    {
        return $this->belongsTo('PApp', 'app_id');
    }

    /**
     * Meta data has many files upload.
     *
     * @return AttachmentRelate
     */
    public function files()
    {
        return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    }

}