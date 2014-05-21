<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Attachment Model
    |--------------------------------------------------------------------------
    |
    | When using the "eloquent" driver, we need to know which
    | Eloquent models should be used throughout Up.
    |
    */

    'attachments' => array(

        //'model' => '\Teepluss\Up\Attachments\Eloquent\Attachment'
        'model' => '\Attachment'

    ),

    /*
    |--------------------------------------------------------------------------
    | Attachment Relate Model
    |--------------------------------------------------------------------------
    |
    | When using the "eloquent" driver, we need to know which
    | Eloquent models should be used throughout Up.
    |
    */

    'attachmentRelates' => array(

        //'model' => '\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate'
        'model' => '\AttachmentRelate'

    ),

    /*
    |--------------------------------------------------------------------------
    | Callback
    |--------------------------------------------------------------------------
    |
    | Placeholder for image not found.
    |
    */

    'placeholder' => function($attachmentId)
    {
        preg_match('|(.*)_(.*)|', $attachmentId, $matches);

        $scale = array_get($matches, "2");

        if ($scale)
        {
            $scale = Config::get("up::uploader.scales.{$scale}");
            if ($scale)
            {
                $size = $scale[1];
            }
        }

        if (empty($size))
        {
            $size = 250;
        }

        $placeholderAvailableSize = array(105, 150, 338, 1000);
        rsort($placeholderAvailableSize);
        $sizeChecker = $size;
        foreach ($placeholderAvailableSize as $value) {
            if ($value > $sizeChecker)
            {
                $size = $value;
            }
        }

        return URL::asset("themes/admin/assets/images/placeholder/image-not-found-{$size}.jpg");
    }

);