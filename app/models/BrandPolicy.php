<?php

class PolicyBrand extends PCMSModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'brand_policy';

    protected $fillable = array('detail');

    public static $rules = array(
        'detail' => 'required',
    );

    /**
     * Brand has many files upload.
     * @return AttachmentRelate
     */
    // public function files()
    // {
    //     return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    // }

    // public function getLogoAttribute($value)
    // {
    //     if ( ! $this->files->isEmpty() )
    //     {
    //         return UP::lookup($this->files->first()->attachment_id);
    //     }

    //     return '';
    // }

    // public function getLogoThumbAttribute($value)
    // {
    //     if ( ! $this->files->isEmpty() )
    //     {
    //         return UP::lookup($this->files->first()->attachment_id)->scale('square');
    //     }

    //     return '';
    // }

}