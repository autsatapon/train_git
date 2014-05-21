<?php

class MigrateCollection extends PCMSModel {

    protected $fillable = array('parent_id', 'attachment_id', 'name', 'slug', 'is_category');

	protected $table = 'migrated_collections';

    /**
     * Brand has many files upload.
     * @return AttachmentRelate
     */
    public function files()
    {
        return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    }



}