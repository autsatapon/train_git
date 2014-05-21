<?php

class MetasController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme = Theme::uses('admin')->layout('dialog');
    }

    public function getIndex()
    {

    }

    public function getCreate($appId, $model, $contentId)
    {
        // Current application.
        $app = PApp::find($appId);

        // Content that need meta.
        $content = $model::find($contentId);

        $exists = $content->metadatas()->whereAppId($appId)->lists('key');
        $exists = ($exists) ?: array(0);

        // Meta available for this content.
        $metas = Meta::whereAppId($appId)->whereModel($model)->whereNotIn('key', $exists)->get();

        // Option metas.
        $types = array('-1' => 'Please select') + (array) $metas->lists('title', 'key');

        // Plugin select2.
        $this->theme->asset()->serve('select2');

        return $this->theme->of('metas.create', compact('app', 'content', 'metas', 'types'))->render();
    }

    public function postCreate($appId, $model, $contentId)
    {
        // Current application.
        $app = PApp::find($appId);

        // Content that need meta.
        $content = $model::find($contentId);

        // What key?
        $key = (Input::get('key') == '-1') ? '' : Input::get('key');

        // Make sure meta is available for this application.
        $meta = Meta::whereAppId($app->id)->whereModel($model)->whereKey($key)->first();

        // Filter type.
        $type = $meta->type;

        // Checking case.
        switch ($type)
        {
            case 'link' :
            case 'text' :
            case 'textarea' :

                $value = Input::get($key);

                // Validate input before insert.
                $validator = Validator::make(
                    array(
                        'key' => $key,
                        $key  => $value
                    ),
                    array(
                        'key' => 'required',
                        $key  => 'required'
                    )
                );
                break;
            case 'file' :

                $file = Input::file($key);

                $value = null;

                // Validate input before insert.
                $validator = Validator::make(
                    array(
                        'key' => $key,
                        $key  => $file
                    ),
                    array(
                        'key' => 'required',
                        $key  => 'required|image'
                    )
                );
                break;
        }

        // Ok, all fine.
        if ($validator->passes())
        {
            $data = array(
                'app_id' => $app->id,
                'type'   => $type,
                'key'    => $key,
                'value'  => $value
            );

            $data = new MetaData($data);

            $metadata = $content->metadatas()->save($data);

            // We need to upload when the type set to be file.
            if ($type == 'file' and isset($file))
            {
                $attachment = UP::upload($metadata, $file)->getMasterResult();

                // Force replace with a new size.
                if (isset($meta->options->resize))
                {
                    list ($width, $height) = explode('x', $meta->options->resize);

                    // Load and save to the same place.
                    $res = WideImage\WideImage::load($attachment['location'])->resize($width, $height, 'fill')->crop('center', 'middle', $width, $height);
                    $res->saveToFile($attachment['location']);

                    $attachment['dimension'] = $meta->options->resize;
                }

                $metadata->update(array(
                    'attachment_id' => $attachment['fileName'],
                    'value'         => $attachment['url']
                ));
            }


            $json = json_encode($metadata->toArray());

            $this->theme->asset()->container('embed')->writeScript('callback', '
                $(function() {

                    window.parent.metaInsert('.$json.');
                    window.parent.$.fancybox.close();

                })
            ');

            return $this->theme->string('')->render();
        }

        return Redirect::back()->withInput()->withErrors($validator->messages());
    }

    public function getEdit($id)
    {
        $metadata = MetaData::with('app')->findOrFail($id);

        return $this->theme->of('metas.edit', compact('metadata'))->render();
    }

    public function postEdit($id)
    {
        $metadata = MetaData::with('app')->findOrFail($id);

        $data = array();

        if ($metadata->type == 'file' and $metadata->attachment_id)
        {
            // Remove old attachment.
            $attachmentId = $metadata->attachment_id;
            UP::remove($attachmentId, true);

            // Upload a new one.
            $attachment = UP::upload($metadata, Input::file('value'))->getMasterResult();

            $data = array(
                'attachment_id' => $attachment['fileName'],
                'value'         => $attachment['url']
            );
        }
        else
        {
            $data = array(
                'value' => Input::get('value')
            );
        }

        $metadata->update($data);

        $json = json_encode($metadata->toArray());

        $this->theme->asset()->container('embed')->writeScript('callback', '
            $(function() {

                window.parent.metaUpdate('.$json.');
                window.parent.$.fancybox.close();

            })
        ');

        return $this->theme->string('')->render();
    }

    public function getAjaxDelete($id)
    {
        $metadata = MetaData::findOrFail($id);

        // This type is image upload and use UP feature,
        // so remove attachment file first.
        if ($metadata->type == 'file' and $metadata->attachment_id)
        {
            $attachmentId = $metadata->attachment_id;

            UP::remove($attachmentId, true);
        }

        $metadata->delete();

        return $metadata;
    }

}