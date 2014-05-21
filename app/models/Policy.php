<?php

class Policy extends PCMSModel {

    public static $autoKey = false;

    protected $fillable = array('title', 'description');

    public static $rules = array(
        'title' => 'required|unique:policies',
        'description' => 'required',
        'type' => 'required',
        /* 'policylogo' => 'image|max:2000', */
    );

    public static $labels = array(
		'title' => 'Title',
		 'logo' => 'Logo',
		 'action' => 'Action',
         'created_at' => 'Created At'
    );

    public function note()
    {
        return $this->morphOne('Note', 'noteable');
    }

    public function brands()
    {
        return $this->belongsToMany('Brand');
    }

	public function vendors()
	{
		return $this->belongsToMany('VVendor', 'vendors_policies', 'policy_id', 'vendor_id')->withPivot('id','brand_id', 'policy_title', 'policy_description');
	}


    /**
     * Brand has many files upload.
     * @return AttachmentRelate
     */
    public function files()
    {
        return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    }

    public function getLogoAttribute($value)
    {
        if ( ! $this->files->isEmpty() )
        {
            return UP::lookup($this->files->first()->attachment_id);
        }

        return '';
    }

    public function getLogoThumbAttribute($value)
    {
        if ( ! $this->files->isEmpty() )
        {
            return UP::lookup($this->files->first()->attachment_id)->scale('square');
        }

        return '';
    }

}