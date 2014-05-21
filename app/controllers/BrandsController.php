<?php
class BrandsController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Brands Management', URL::to('brands'));
    }

    public function getIndex()
    {
        $brandData = Brand::orderBy('name')->get();
        $this->data['brandData'] = $brandData;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Brands Management');
        return $this->theme->of('brands.index', $this->data)->render();
    }

    public function getCreate()
    {
        // check referer from ProductNewMaterialController@getIndex
        $header = Request::header();
        if( ! empty($header['referer'][0]))
        {
            if(strpos($header['referer'][0], URL::action('ProductNewMaterialController@getIndex')) !== false)
            {
                // set session referer for postCreate
                Session::put('BrandsControllerCreateReferer', $header['referer'][0]);
            }
        }

        $this->data['formData'] = array(
            'name' => '',
            'description' => '',
            'note' => ''
        );

        $this->theme->breadcrumb()->add('Create Brand', URL::to('brands/create'));
        $this->theme->setTitle('Create Brand');
        return $this->theme->of('brands.create', $this->data)->render();
    }

    public function postCreate()
    {
        $brand = new Brand;
        $brand->name = Input::get('name');
        $brand->slug = Str::slug($brand->name);
        $brand->description = Input::get('description');

        if(Input::get('translate')) {
            $brand->setTranslate('name', Input::get('translate.name'));
            $brand->setTranslate('description', Input::get('translate.description'));
        }

        // Addition rule for validate an Image.
        $brand->addValidate(
            array('image' => Input::file('brandlogo')),
            array('image' => 'image|max:2000')
        );

        if ( !$brand->save() )
        {
            return Redirect::to('brands/create')->withInput()->withErrors($brand->errors());
        }

        if (Input::get('note') != '')
        {
            $brandNote = new Note;
            $brandNote->detail = Input::get('note');
            $brand->note()->save($brandNote);
        }

        // Upload Image;
        if (Input::hasFile('brandlogo'))
        {
            $brandLogo = Input::file('brandlogo');

            // save a new upload image
            $attachment = UP::upload($brand, $brandLogo)->resize()->getMasterResult();

            $brand->attachment_id = $attachment['fileName'];
            $brand->save();
        }

        // check has referer in session
        $referer = Session::get('BrandsControllerCreateReferer');
        if($referer !== null) {
            Session::forget('BrandsControllerCreateReferer');
            Session::put('BrandId', $brand->id);
            return Redirect::to($referer);
        }

        return Redirect::to('brands')->withSuccess('Created successfully');
    }

    public function getEdit($id = 0)
    {
        $brandData = Brand::findOrFail($id);
        $apps = PApp::with(array('metas' => function($q)
        {
            $q->where('model', 'Brand');
        }))
        ->get();


        $files = $brandData->files;
        $logo = $brandData->logo_thumb;
        /*
        $logo = '';
        if ( !$files->isEmpty() )
        {
            $imageId = $files->first()->attachment_id;
            $logo = UP::lookup($imageId)->scale('square');
        }
        */

        $brandNote = $brandData->note;
        $note = '';
        if ( !empty($brandNote) )
        {
            $note = $brandNote->detail;
        }

        $this->data['brand'] = $brandData;
        $this->data['apps'] = $apps;
        $this->data['formData'] = array(
            'name' => $brandData->name,
            'description' => $brandData->description,
            'note' => $note,
            'logo' => $logo
        );

        $this->theme->breadcrumb()->add('Edit Brand', URL::to('brands/edit/'.$id));
        $this->theme->setTitle('Edit Brand');
        return $this->theme->of('brands.edit', $this->data)->render();
    }

    public function postEdit($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->name = Input::get('name');
        $brand->slug = Str::slug($brand->name);
        $brand->description = Input::get('description');

        if(Input::get('translate')) {
            $brand->setTranslate('name', Input::get('translate.name'));
            $brand->setTranslate('description', Input::get('translate.description'));
        }

        // Addition rule for validate an Image.
        $brand->addValidate(
            array('image' => Input::file('brandlogo')),
            array('image' => 'image|max:2000')
        );

        if ( !$brand->save() )
        {
            return Redirect::to('brands/edit/'.$id)->withInput()->withErrors($brand->errors());
        }

        $brandNote = $brand->note;

        if ( !empty($brandNote) )
        {
            $brandNote->detail = Input::get('note');
            $brandNote->save();
        }
        else
        {
            $brandNote = new Note;
            $brandNote->detail = Input::get('note');
            $brand->note()->save($brandNote);
        }

        $image = Input::file('brandlogo');
        if (!empty($image))
        {
            // Remove old upload image (if exist)
            if ( !empty($brand->attachment_id) )
            {
                UP::remove($brand->attachment_id);
            }
            /*
            if ( !$brand->files->isEmpty() )
            {
                UP::remove($brand->files->first()->attachment_id);
                $brand->files()->first()->delete();
            }
            */

            // save a new upload image
            $attachment = UP::upload($brand, $image)->resize()->getMasterResult();

            $brand->attachment_id = $attachment['fileName'];
            $brand->save();
        }

        return Redirect::back()->withSuccess('Saved successfully');
    }

}