<?php

class MediaContent extends PCMSModel {

	protected $table = 'media_contents';
	protected $fillable = array('mode', 'meta', 'sort_order');

	public static $rules = array(
		'mode' => 'in:image,youtube,360',
		'sort_order' => 'integer'
	);


	public static function boot()
    {
    	parent::boot();

    	static::creating(function($model)
		{
			$user = Sentry::getUser();
			if ( $user )
			{
				$model->author_id = $user->id;
			}
		});
	}

	// public function getSubpath()
	// {
	// 	$up = UP::getFacadeRoot();
	// 	$up->inject(array(
	// 		'subpath' => 'xxx'
	// 	));

	// 	return $up;
	// }


	public function mediable()
	{
		return $this->morphTo();
	}

	public function files()
	{
		return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
	}

	public function getImageAttribute()
	{
		return UP::lookup($this->attachment_id);
	}

	public function getThumbnailAttribute()
	{
		return $this->image->scale('square');
	}

	public function getLinkAttribute()
	{
		if ($this->mode == 'youtube')
		{
			$meta = json_decode($this->meta);

			return $meta->link;
		}
		elseif ($this->mode == 'image' or $this->mode == '360')
		{
			$imgPath = UP::lookup($this->attachment_id)->get();

			return $imgPath;
		}

		return;
	}

	public function scopeOnlyMode($query, $mode)
	{
		return $query->whereMode($mode);
	}

}