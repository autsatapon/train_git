<?php

class MigrateBrand extends PCMSModel {

    protected $fillable = array('name', 'description');

	protected $table = 'migrated_brands';

    /**
     * Brand has many files upload.
     * @return AttachmentRelate
     */
    public function files()
    {
        return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    }



}