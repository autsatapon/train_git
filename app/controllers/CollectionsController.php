<?php

class CollectionsController extends AdminController {

    public function __construct()
    {
        parent::__construct();

        $this->theme->breadcrumb()->add('Collections Management', URL::to('collections'));
    }

    public function getIndex()
    {
        $parentId = (int) Input::get('parent_id');

        if ($parentId > 0)
        {
            $collections = Collection::with('apps')->where('parent_id', '=', $parentId)->where('collection_type', '=', 'default')->get();

            $parentCollection = Collection::with('apps')->whereId($parentId)->first();

            $this->data['parentCollection'] = $parentCollection;

            $title = "Collections Management | Sub-collection of {$parentCollection->name}";
        }
        else
        {
            $collections = Collection::with('apps')->RootCollection()->get();
            $title = 'Collections Management';
        }

        $this->data['parentId'] = $parentId;
        $this->data['collections'] = $collections;



        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle($title);
        return $this->theme->of('collections.index', $this->data)->render();
    }

    public function getCreate()
    {
        $apps = PApp::all();
        $this->data['apps'] = $apps;
        $this->data['formData'] = array(
            'name' => '',
            'slug' => '',
            'is_category' => '0',
        );

        $parentId = (int) Input::get('parent_id');
        $this->data['parentId'] = $parentId;

        $inputApp = (Input::old('app') !== NULL) ? Input::old('app') : array() ;
        $this->data['inputApp'] = $inputApp;

        if ($parentId > 0)
        {
            $parentCollection = Collection::find($parentId);
            // $parentCollectionName = Collection::whereId($parentId)->pluck('name');
            $parentCollectionName = $parentCollection->name;
            $title = "Create Child of \"{$parentCollectionName}\" Collection.";
            $createUrl = URL::to("collections/create?parent_id={$parentId}");

            // Disable Apps (Parent Collection doesn't use these apps)
            $disableApps = $apps->lists('id');
            if ( !$parentCollection->apps->isEmpty() )
            {
                $disableApps = array_diff($disableApps, $parentCollection->apps->lists('id'));
            }

            $this->data['disableApps'] = $disableApps;
        }
        else
        {
            $title = 'Create Collection';
            $createUrl = URL::to('collections/create');

            $this->data['disableApps'] = array();
        }
        $this->data['collection'] = null;
        $this->theme->breadcrumb()->add($title, $createUrl);
        $this->theme->setTitle($title);
        return $this->theme->of('collections.create-edit', $this->data)->render();
    }

    public function postCreate()
    {
        $parentId = (int) Input::get('parent_id');

        $collection = new Collection;
        $collection->name = Input::get('name');

        // $collection->slug = Str::slug($collection->name);
        $collection->slug = Input::get('slug');
        if ( empty($collection->slug) )
        {
            if(Input::get('translate'))
            {
                $collection->slug = Input::get('translate.name');
            }

            if ( empty($collection->slug) )
            {
                $collection->slug = $collection->name;
            }
        }
        $collection->slug = Str::slug($collection->slug);

        $collection->is_category = (Input::get('is_category') != NULL) ? 1 : 0 ;
        $collection->parent_id = $parentId;

        if(Input::get('translate')) {
            $collection->setTranslate('name', Input::get('translate.name'));
        }

        // Addition rule for validate an Image.
        $collection->addValidate(
            array('image' => Input::file('image')),
            array('image' => 'image|max:2000')
        );

        if ( ! $collection->save() )
        {
            return Redirect::back()->withErrors($collection->errors())->withInput();
        }

        // save apps relationships
        if (Input::get('app') != NULL)
        {
            $collection->apps()->sync(Input::get('app'));
        }

        // Upload Image;
        $image = Input::file('image');
        if (!empty($image))
        {
            // save a new upload image
            // UP::upload($collection, $image)->resize();
            $attachment = UP::upload($collection, $image)->resize()->getMasterResult();

            $collection->attachment_id = $attachment['fileName'];
            $collection->save();
        }

        if ($parentId > 0)
        {
            return Redirect::to("collections?parent_id={$parentId}");
        }

        return Redirect::to('collections');
    }

    public function getEdit($id)
    {
        $collection = Collection::findOrFail($id);

        $apps = PApp::with(array('metas' => function($q)
        {
            $q->where('model', 'Collection');
        }))
        ->get();

        $this->data['collection'] = $collection;
        $this->data['apps'] = $apps;

        $this->data['formData'] = array(
            'name' => $collection->name,
            'slug' => $collection->slug,
            'is_category' => $collection->is_category,
        );

        $collectionApps = $collection->apps;
        $collectionAppLists = ( !empty($collectionApps) ) ? $collection->apps->lists('id') : array() ;
        $this->data['inputApp'] = Input::old('app', $collectionAppLists);

        $this->data['collectionImageThumb'] = $collection->thumbnail;


        $parentId = $collection->parent_id;

        if ($parentId > 0)
        {
            $parentCollection = Collection::find($parentId);

            // Disable Apps (Parent Collection doesn't use these apps)
            $disableApps = $apps->lists('id');
            if ( !$parentCollection->apps->isEmpty() )
            {
                $disableApps = array_diff($disableApps, $parentCollection->apps->lists('id'));
            }

            $this->data['disableApps'] = $disableApps;
        }
        else
        {
            $this->data['disableApps'] = array();
        }

        $this->theme->breadcrumb()->add('Edit Collection', URL::to('collections/edit/'.$id));
        $this->theme->setTitle('Edit Collection');

        return $this->theme->of('collections.create-edit', $this->data)->render();
    }

    public function postEdit($id)
    {
        $parentId = (int) Input::get('parent_id');

        $collection = Collection::findOrFail($id);
        $collection->name = Input::get('name');

        // $collection->slug = Str::slug($collection->name);
        $collection->slug = Input::get('slug');
        if ( empty($collection->slug) )
        {
            if(Input::get('translate'))
            {
                $collection->slug = Input::get('translate.name.en_US');
            }

            if ( empty($collection->slug) )
            {
                $collection->slug = $collection->name;
            }
        }

        $collection->slug = Str::slug($collection->slug);

        $collection->is_category = (Input::get('is_category') != NULL) ? 1 : 0 ;

        if(Input::get('translate')) {
            $collection->setTranslate('name', Input::get('translate.name'));
        }

        /*
        $collection->meta_title = Input::get('meta_title');
        $collection->meta_keywords = Input::get('meta_keywords');
        $collection->meta_description = Input::get('meta_description');
        */

        $collection->parent_id = $parentId;

        // Addition rule for validate an Image.
        $collection->addValidate(
            array('image' => Input::file('image')),
            array('image' => 'image|max:2000')
        );

        if ( ! $collection->save() )
        {
            return Redirect::back()->withErrors($collection->errors())->withInput();
        }

        // save apps relationships
        if (Input::get('app') != NULL)
        {
            $collection->apps()->sync(Input::get('app'));
        }
        else
        {
            $collection->apps()->detach();
        }

        $image = Input::file('image');
        if (!empty($image))
        {
            // Remove old upload image (if exist)
            if ( !empty($collection->attachment_id) )
            {
                UP::remove($collection->attachment_id);
            }
            /*
            if ( !$collection->files->isEmpty() )
            {
                UP::remove($collection->files->first()->attachment_id);
                $collection->files()->first()->delete();
            }
            */

            // save a new upload image
            // UP::upload($collection, $image)->resize();
            $attachment = UP::upload($collection, $image)->resize()->getMasterResult();

            $collection->attachment_id = $attachment['fileName'];
            $collection->save();
        }

        // Update & Remove Products in this collection
        $products = $collection->products;
        if ( !$products->isEmpty() )
        {
            foreach ($products as $product)
            {
                ElasticUtils::removeProduct($product);
                ElasticUtils::updateProduct($product);
            }
        }

        // return Redirect::to('collections');
        if ($parentId > 0)
        {
            return Redirect::to("collections?parent_id={$parentId}");
        }

        return Redirect::to('collections');
    }

    public function getProducts($collectionId)
    {
        $title = 'Products in Collection';

        $collection = Collection::with('products', 'products.mediaContents', 'bestSeller')->findOrFail($collectionId);

        $products = $collection->products;

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

        $this->data['products'] = $products;
        $this->data['collection'] = $collection;

        $this->theme->breadcrumb()->add($title, URL::to("collections/products/{$collectionId}"));
        $this->theme->setTitle($title);
        return $this->theme->of('collections.products', $this->data)->render();
    }

    public function getBestSeller($bestsellerId,$parent_id = 0)
    {
        $title = 'Best Seller Products in Collection';

        if($bestsellerId > 0){
            $collection = Collection::with('products', 'products.mediaContents')->findOrFail($bestsellerId);

            $products = $collection->products;

            $this->data['products'] = $products;
            $this->data['collection'] = $collection;
        }else{
            $collection = Collection::with('products', 'products.mediaContents')->findOrFail($parent_id);

            $this->data['products'] = '';
            $this->data['collection'] = $collection;
        }

        $this->theme->breadcrumb()->add($title, URL::to("collections/best-seller/{$bestsellerId}/{$parent_id}"));
        $this->theme->setTitle($title);
        return $this->theme->of('collections.best-seller', $this->data)->render();
    }

    public function getSetBestSeller($parent_id ,$bestsellerId =0)
    {
        $title = 'Set Best Seller Products in Collection';




            if($bestsellerId > 0){

                $collection = Collection::findOrFail($parent_id);

                // $product_collections = DB::table('product_collections')->where('collection_id', $parent_id)->where('collection_id','!=', $bestsellerId)->get();
                $a1 = DB::table('product_collections')->where('collection_id', $parent_id)->lists('product_id');
                $a2 = DB::table('product_collections')->where('collection_id', $bestsellerId)->lists('product_id');
                $a3 = array_diff($a1, $a2);

                if (!empty($a3))
                {
                    $products = Product::whereIn('id', $a3)->get();
                }
                else
                {
                    $products = array();
                }

                $this->data['products'] = $products;
            }else{

                $collection = Collection::with('products', 'products.mediaContents')->findOrFail($parent_id);

                $products = $collection->products;

                $this->data['products'] = $products;
            }


            $this->data['collection'] = $collection;


        $this->theme->breadcrumb()->add($title, URL::to("collections/set-best-seller/{$parent_id}/{$bestsellerId}"));
        $this->theme->setTitle($title);
        return $this->theme->of('collections.set-best-seller', $this->data)->render();
    }

    public function getDeleteBestSeller($product_id ,$bestsellerId,$parent_id)
    {

        $product = Product::findOrFail($product_id);

        $product->collections()->detach($bestsellerId);

        // return Redirect::to('collections');
        if ($parent_id > 0)
        {
            return Redirect::to("collections/best-seller/{$bestsellerId}/{$parent_id}")->withSuccess('Deleted');
        }

        return Redirect::to('collections');
    }

    public function postInsertBestSeller($parent_id ,$bestsellerId =0)
    {
        $productIdArr = Input::get('product');

        if (empty($productIdArr))
        {
            return Redirect::to("collections/set-best-seller/{$parent_id}/{$bestsellerId}")->withErrors('Please choose product first.');
        }

        if ($bestsellerId == 0)
        {
            $collection = new Collection;
            $collection->parent_id = $parent_id;
            $collection->name = 'Best Seller';
            $collection->is_category = 0;
            $collection->collection_type = 'best_seller';
            $collection->save();
            $bestsellerId = $collection->id;
        }
        else
        {
            $collection = Collection::find($bestsellerId);
        }

        foreach ($productIdArr as $productId)
        {
            $collection->products()->attach($productId);
        }

        return Redirect::to("collections/best-seller/{$bestsellerId}/{$parent_id}")->withSuccess('Completed.');
    }

    // public function postEditmetas($appId, $collectionId)
    // {
    //     $collection = Collection::find($collectionId);

    //     $keys = Input::get('keys');
    //     $values = Input::get('values');

    //     foreach ($keys as $i => $key)
    //     {
    //         $value = $values[$i];

    //         if ( ! $value) continue;

    //         $data = array(
    //             'app_id' => $appId,
    //             'key'    => $key,
    //             'value'  => $value
    //         );

    //         $metadata = $collection->metadatas()->whereAppId($appId)->whereKey($key)->first();

    //         if ($metadata)
    //         {
    //             $metadata->update($data);
    //         }
    //         else
    //         {
    //             $data = new MetaData($data);

    //             $collection->metadatas()->save($data);
    //         }

    //     }

    //     return Redirect::back();
    // }

}