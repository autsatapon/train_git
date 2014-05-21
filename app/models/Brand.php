<?php

class Brand extends PCMSModel {

	protected $fillable = array('name', 'description');

    public static $rules = array(
        'name' => 'required|unique:brands',
        /* 'brandlogo' => 'image|max:2000', */
    );

    public static $labels = array(
        'name' => 'Brand Name',
        'pkey' => 'Brand Key',
        'description' => 'Description'
    );

    /*
	public static $rules = array(
        // array('name', 'required', 'unique:brands', 'on' => 'creating'),
        // array('name', 'required', 'on' => 'updating'),
        // array('brandlogo', 'required', 'image', 'max:2000', 'on' => 'creating'),
        // array('brandlogo', 'image', 'max:2000', 'on' => 'updating'),
        array('name', 'required', 'unique:brands'),
        array('description', 'required', 'on' => 'updating'),
        array('brandlogo', 'image', 'max:2000'),
    );
    */

	/*
	public static $rules = array(
	    'name'			=> 'required',
	    'description'	=> 'required'
	  );
	*/

	/*
	public static $customMessages = array(
    	'name.required' => 'กรุณากรอกชื่อ Brand ด้วยค่ะ',
        'description.required' => 'กรุณากรอกรายละเอียดของ Brand ด้วยค่ะ',
        'brandlogo.required' => 'กรุณาเลือก Upload รูปภาพโลโก้ของ Brand ด้วยค่ะ',
        'brandlogo.image' => 'กรุณาเลือก Upload เฉพาะไฟล์รูปภาพเท่านั้นค่ะ',
  	);
	*/
    public function products()
    {
        return $this->hasMany('Product');
    }

	// public function policies()
	// {
	// 	return $this->belongsToMany('Policy')->withPivot('detail');
	// }

    public function policies()
    {
        return $this->morphMany('PolicyRelate', 'policiable');
    }

    public function note()
    {
        return $this->morphOne('Note', 'noteable');
    }

    public function metadatas()
    {
        return $this->morphMany('MetaData', 'metadatable');
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
        if (!empty($this->attributes['attachment_id']))
        {
            return UP::lookup($this->attributes['attachment_id']);
        }

        /*
        if ( ! $this->files->isEmpty() )
        {
            return UP::lookup($this->files->first()->attachment_id);
        }
        */

        return '';
    }

    public function getLogoThumbAttribute($value)
    {
        if (!empty($this->attributes['attachment_id']))
        {
            return UP::lookup($this->attributes['attachment_id'])->scale('square');
        }

        /*
        if ( ! $this->files->isEmpty() )
        {
            return UP::lookup($this->files->first()->attachment_id)->scale('square');
        }
        */

        return '';
    }

    public function getThumbnailAttribute()
    {
        if (!empty($this->attributes['attachment_id']))
        {
            return (string) UP::lookup($this->attributes['attachment_id']);
        }

        return '';
    }

    public function getMetasAttribute()
    {
        if ($this->metadatas->isEmpty())
        {
            return array();
        }

        $rawArr = $this->metadatas->toArray();

        $metas = array();
        foreach ($rawArr as $meta)
        {
            $metas[$meta['key']] = $meta['value'];
        }

        return $metas;
    }

}