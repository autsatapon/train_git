<?php
class PoliciesController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Policy Management', URL::to('policies'));
    }

    public function getIndex()
    {
		$policyData = Policy::with('files')->orderBy('created_at', 'desc')->get();
        $this->data['policyData'] = $policyData;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Policy Management');

        return $this->theme->of('policies.index', $this->data)->render();
    }

    public function getCreate()
    {
        $policyType = Config::get('global.policy_type');
        array_unshift($policyType, array("-- Select --"));
        foreach ($policyType as $index => $type) {
            $policyType[$index] = implode(" / ", $type);
        }
        $this->data['policyType'] = $policyType;

		$this->data['formData'] = array(
            'title' => '',
            'description' => '',
        );

        $this->theme->breadcrumb()->add('Create Policy', URL::to('policies/create'));
        $this->theme->setTitle('Create Policy');

        return $this->theme->of('policies.create', $this->data)->render();
    }

    public function postCreate()
    {
		$policy = new Policy;
        $policy->title = Input::get('title');
        $policy->description = Input::get('description');
        if (Input::get('type'))
        {
            $policy->type = Input::get('type');
        }
        else
        {
            $policy->type = "";
        }

        if (Input::get('translate'))
        {
            $translate = Input::get('translate');

            if (!empty($translate['title']))
            {
                $policy->setTranslate('title', $translate['title']);
            }

            if (!empty($translate['description']))
            {
                $policy->setTranslate('description', $translate['description']);
            }
        }

        // Addition rule for validate an Image.
        $policy->addValidate(
            array('image' => Input::file('policylogo')),
            array('image' => 'image|max:2000')
        );

		if ( ! $policy->save())
        {
            return Redirect::to('policies/create')->withInput()->withErrors($policy->errors());
        }

        // Upload Image;
        if (Input::hasFile('policylogo'))
        {
            $policyLogo = Input::file('policylogo');

            // save a new upload image
            UP::upload($policy, $policyLogo)->resize();
        }

        return Redirect::to('policies');
    }

    public function getEdit($id = 0)
    {
        $policyType = Config::get('global.policy_type');
        array_unshift($policyType, array("-- Select --"));
        foreach ($policyType as $index => $type) {
            $policyType[$index] = implode(" / ", $type);
        }
        $this->data['policyType'] = $policyType;

        $policyData = Policy::findOrFail($id);

        $files = $policyData->files;
        $logo = '';

        if ( ! $files->isEmpty())
        {
            $imageId = $files->first()->attachment_id;
            $logo = UP::lookup($imageId);
        }

        $this->data['policy'] = $policyData;

        $this->data['formData'] = array(
            'title'       => $policyData->title,
            'description' => $policyData->description,
            'type'        => $policyData->type,
            'logo'        => $logo
        );

        $this->theme->breadcrumb()->add('Edit Policy', URL::to('policies/edit/'.$id));
        $this->theme->setTitle('Edit Policy');

        return $this->theme->of('policies.edit', $this->data)->render();
    }

    public function postEdit($id)
    {
		$policyData = Policy::findOrFail($id);
        $policyData->title = Input::get('title');
        $policyData->description = Input::get('description');
        if (Input::get('type'))
        {
            $policyData->type = Input::get('type');
        }
        else
        {
            $policyData->type = "";
        }

        if(Input::get('translate')) {
            $translate = Input::get('translate');

            if (!empty($translate['title']))
            {
                $policyData->setTranslate('title', $translate['title']);
            }

            if (!empty($translate['description']))
            {
                $policyData->setTranslate('description', $translate['description']);
            }
        }

        // Addition rule for validate an Image.
        $policyData->addValidate(
            array('image' => Input::file('policylogo')),
            array('image' => 'image|max:2000')
        );

        if ( !$policyData->save() )
        {
            return Redirect::to('policies/edit/'.$id)->withInput()->withErrors($policyData->errors());
        }

        $image = Input::file('policylogo');
        if (!empty($image))
        {
            // Remove old upload image (if exist)
            if ( !$policyData->files->isEmpty() )
            {
                UP::remove($policyData->files->first()->attachment_id);
               // $policyData->files()->first()->delete();
            }

            // save a new upload image
            UP::upload($policyData, $image)->resize();
        }

        return Redirect::to('policies');
    }

}