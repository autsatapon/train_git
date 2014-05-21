<?php

use WideImage\WideImage;

class ProductNewMaterialController extends AdminController {

    protected $step = array();

    protected $prefixSession = "";

    protected $product = null;

    public function __construct()
    {
        parent::__construct();
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'New Material',
                'url'   => URL::action('ProductNewMaterialController@getIndex')
            )
        ));

        // setup new material step
        $this->step = array(
            '1' => array('url' => URL::action('ProductNewMaterialController@getIndex'), 'icon' => 'icol-text-padding-top', 'label' => 'Choose/Create Product'),
            '2' => array('url' => URL::action('ProductNewMaterialController@getStep2'), 'icon' => 'icol-text-list-bullets', 'label' => 'Choose Material'),
            '3' => array('url' => URL::action('ProductNewMaterialController@getStep3'), 'icon' => 'icol-textfield', 'label' => 'Manage variant type'),
            '4' => array('url' => URL::action('ProductNewMaterialController@getStep4'), 'icon' => 'icol-textfield', 'label' => 'Set Variant Option'),
            '5' => array('url' => URL::action('ProductNewMaterialController@getSummary'), 'icon' => 'icol-accept', 'label' => 'Summary')
        );

        // setup prefix key for cache use during organize new material process
        $userLogin = Sentry::getUser();
        $this->prefixSession = 'new-material_uid-'.$userLogin->id;

        //d(Session::get($this->prefixSession));

        // create product query instance
        $this->product = Product::withTrashed()
                        ->where(function($query){
                            $query->where(function($query){
                                        $query->where('status', '!=', 'incomplete')->whereNull('deleted_at')->whereActive(0);
                                    })
                                  ->orWhere(function($query){
                                        $query->where('status', '=', 'incomplete')->whereNotNull('deleted_at');
                                    });
                        });
    }

    /**
     * index page - product management
     * @return response
     */
    public function getIndex()
    {
        $this->storageDestroy();

        $this->theme->asset()->usePath()->add('bootstrap-tab', 'bootstrap/css/bootstrap-tab.css', array('bootstrap'));
        $this->theme->asset()->container('footer')->usePath()->add('bootstrap-tab', 'bootstrap/js/bootstrap-tab.js', array('bootstrap'));

        $mode = Input::old('choose', Input::old('create', 'choose'));

        // get brand id session - it's from brand controller created
        $brandId = Session::get('BrandId');
        $brandLastCreated = null;
        if($brandId)
        {
            $mode = "create";
            $brandLastCreated = Brand::find($brandId);
        }

        $importedMaterials = ImportedMaterial::orderBy('linesheet', 'asc')
            ->newMaterial()
            ->groupBy("linesheet")
            ->select("linesheet", DB::raw("count(id) as count"))
            ->distinct()
            ->get();

        $selectLineSheet = array();
        foreach ($importedMaterials as $key => $importedMaterial)
        {
            $selectLineSheet[$importedMaterial['linesheet']] = "({$importedMaterial['count']}) {$importedMaterial['linesheet']}";
        }

        // get all brand
        $brand = Brand::orderBy('name')->lists('name', 'id');

        $view_content = compact('mode', 'brand', 'brandLastCreated', 'selectLineSheet');

        $content = View::make('products.new-material.index', $view_content);
        return $this->formWizard(1, $content );
    }

    public function postIndex()
    {
        $skipToStep3 = false;

        // check this request is search or set
        $mode = strtolower(Input::get('choose', Input::get('create', 'choose')));

        $errorResponse = function($error)
        {
            return Redirect::back()
                ->withErrors($error)
                ->withInput();
        };

        if (! Input::get('product-id_manage-variant'))
        {
            // check linesheet that user sended
            $lineSheet = ImportedMaterial::whereLinesheet(Input::get("line_sheet"))
                    ->newMaterial()
                    ->select("id", "linesheet")
                    ->first();

            // check injection case
            if (! $lineSheet)
            {
                // linesheet not found
                return $errorResponse("Please select line sheet before go to choose materials");
            }
        }

        if($mode == 'choose') {
            // choose tab

            // get product id for manage variant
            $productId = Input::get('product-id_manage-variant');

            $modelProduct = $this->product;

            if ($productId)
            {
                $product = $modelProduct->find($productId);

                // check for injection case
                if (! $product)
                {
                    return $errorResponse("Product isn't exists. You can't manage variant on it.");
                }

                if ($product == 'incomplete')
                {
                    return $errorResponse("Product is incomplete. You don't have variant to manage.");
                }

                $skipToStep3 = true;
            }
            else
            {
                // get product id for add new material
                $productId = Input::get('product-id_add-new-material');

                if ($productId)
                {
                    $product = $modelProduct->find($productId);

                    // check for injection case
                    if (! $product)
                    {
                        return $errorResponse("Product isn't exists. You can't add new variant on it.");
                    }
                }
            }

        } else {
            // create tab
            $input = Input::all();
            $input['title'] = trim(@$input['product_name']);

            // insert data to product table - validate with harvey
            $rules = array(
                'product_name' => 'required|unique:products,title',
                'brand'        => 'required|integer|exists:brands,id',
                'translate.title.en_US' => 'required'
                );

            $messages = array(
                'translate.title.en_US.required' => 'Product name need translate to english.'
            );

            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails())
            {
                return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $brand = Brand::find(Input::get('brand'));

            // check for injection case
            if (! $brand)
            {
                return $errorResponse("Brand isn't exists. You can't create product on it.");
            }

            $product = new Product;
            $product->title = trim(Input::get('product_name'));
            $product->product_line = Input::get('line_sheet');
            $product->status = "incomplete";

            if (Input::has('translate'))
            {
                $translate = Input::get('translate');
                $product->setTranslate('title', $translate['title']);
            }

            if (! $product->save())
            {
                return $errorResponse("Can't create new product");
            }

            $product->brand()->associate($brand);

            $product->save();
        }

        // get product id to storage
        $this->storagePut('product_id', $product->getKey());

        if($mode != 'choose') {
            // delete it because deleted_at will use as flag for checking new product.
            // $product->delete();

            $product->{Product::DELETED_AT} = $product->fromDateTime($product->freshTimestamp());
            $product->save();
        }

        // destroy session that don't use
        Session::forget('BrandId');

        if ($skipToStep3)
        {
            // save state
            $this->storagePut('state', 3);
            $this->storageForget('line_sheet');

            return Redirect::action('ProductNewMaterialController@getStep3');
        }
        else
        {
            // save state
            $this->storagePut('state', 2);
            $this->storagePut('line_sheet', Input::get("line_sheet"));

            return Redirect::action('ProductNewMaterialController@getStep2');
        }

    }

    /**
     * search product via ajax
     * @return string HTML
     */
    public function postProductSearch()
    {
        // get keyword from input

        // search in product table
        $products = $this->product->with(array('brand', 'mediaContents'));

        // get product name keywork
        $productName = Input::get('product_name');
        if($productName) {
            $products->where('title', 'LIKE', '%'.$productName.'%');
        }

        // // get product line keyword
        // $productLine = Input::get('product_line');
        // if($productLine)
        // {
        //     $products->where('product_line', 'LIKE', '%'.$productLine.'%');
        // }

        // query it
        $products = $products->take(20)->get();

        if($products->count() > 0) {
            // search found
            $html = "";
            foreach($products as $product)
            {
                $productImage = null;
                $mediaImage = $product->mediaContents->first();
                if ( !empty($mediaImage) )
                {
                    $productImage = (string) UP::lookup($mediaImage->attachment_id)->scale('s');
                    $productImage = "<img src='{$productImage}' style='margin-right: 10px; width: 40px;'>";
                }

                $label = ($product->status == 'incomplete')
                        ? ' <span class="label label-warning">Incomplete</span>'
                        : '';

                $html .= '
                    <tr>
                        <td>
                            '.$productImage.$product->title.$label.'
                        </td>
                        <td>'.$product->product_line.'</td>
                        <td>'.$product->brand->name.'</td>
                        <td>
                        ';
                if ($product->status != 'incomplete')
                {
                    $html .= '<button type="submit" class="btn" name="product-id_manage-variant" value="'.$product->getKey().'">'.__('Manage variant').'</button>';
                }

                $html .= '
                            &nbsp;
                            <button type="submit" class="btn" name="product-id_add-new-material" value="'.$product->getKey().'">'.__('Add new material').'</button>
                        </td>
                    </tr>
                ';
            }
            return $html;
        } else {
            // search not found
            return '<tr><td colspan="4" style="text-align: center;"> Product not found. </td></tr>';
        }
    }

    public function getStep2()
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $lineSheet = $this->storageGet('line_sheet');

        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 2 || ! $productId) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process. (2)');
        }

        // get material id that submited
        $materialsId = $this->storageGet('materials_id', array());

        $this->theme->asset()->usePath()->add('wizard', 'custom-plugins/wizard/wizard.css', array('colorpicker'));

        $data = ImportedMaterial::newMaterial()
                    ->orderByRaw('CASE linesheet WHEN ? THEN 0 ELSE 1 END ASC', array($lineSheet))
                    ->take(50)
                    ->get();

        $view_content = compact('data', 'materialsId', 'lineSheet');
        $content = View::make('products.new-material.step2', $view_content);
        return $this->formWizard(2, $content);
    }

    public function postStep2()
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 2 || ! $productId) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        // save inv id - has many variants
        $materials_id = array();
        $pick = Input::get('pick', null);
        $ids = Input::get('id', null);
        if($pick)
        {
            $materials_id[] = $pick;
        } else if($ids !== null)
        {
            $materials_id = $ids;
        }

        // double check get only material that exists
        if(count($materials_id) > 0) {
            $materials_id = ImportedMaterial::whereIn('id', $materials_id)->lists('id');
        }

        if(count($materials_id) > 0) {
            // save state

            // save inventory
            $this->storagePut('materials_id', $materials_id);

            // get product
            $product = $this->product->with('styleTypes')->findOrFail($productId);

            // check product is incomplete
            if ($product->status == 'incomplete')
            {
                // we must set basic style type to product if data from SC is exists

                // get materials
                $materials = ImportedMaterial::whereIn('id', $materials_id)->get();

                $exists = array();

                // check each material for find style option value from supply chain
                foreach ($materials as $key => $material) {
                    if ($material->color !== null && $material->color != "" && $material->color != "-")
                    {
                        $exists['color'] = '';
                    }
                    if ($material->size !== null && $material->size != "" && $material->size != "-")
                    {
                        $exists['size'] = '';
                    }
                    if ($material->surface !== null && $material->surface != "" && $material->surface != "-")
                    {
                        $exists['surface'] = '';
                    }
                }

                $count = 1;
                // action on every style type exists
                foreach ($exists as $type => $value) {

                    // try to get style type model
                    $method = 'getStyleType'.ucfirst($type);
                    $styleType = $this->$method();

                    if ($styleType)
                    {
                        if (! $product->styleTypes->find($styleType->getKey()))
                        {
                            if(
                                ($count == 1 && ! isset($exists['color']))
                                || $type == 'color'
                            )
                            {
                                $product->styleTypes()->attach($styleType->getKey(), array('media_set' => 1));
                            }
                            else
                            {
                                $product->styleTypes()->attach($styleType->getKey());
                            }
                            $count++;
                        }
                    }
                }
            }

            $this->storagePut('state', 3);

            return Redirect::action('ProductNewMaterialController@getStep3');
        } else {
            return Redirect::back()->with('warning', 'You must select at least one material.');
        }
    }

    public function getStep3()
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 3 || ! $productId) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process. (3)');
        }

        $product = $this->product->with('styleTypes.translates', 'mediaContents', 'brand')->findOrFail($productId);


        $product->productImage = null;
        $mediaImage = $product->mediaContents->first();
        if ( !empty($mediaImage) )
        {
            $productImage = (string) UP::lookup($mediaImage->attachment_id)->scale('s');
        }
        else
        {
            $productImage = URL::asset("themes/admin/assets/images/placeholder/image-not-found-105.jpg");
        }

        $product->productImage = $productImage;

        $styleTypesId = $product->styleTypes->lists('id');

        $styleType = StyleType::query();
        if ($styleTypesId)
        {
            $styleType->whereNotIn('id', $styleTypesId);
        }
        $styleType = $styleType->get();

        $selectStyleType =  array("0" => "-- Select --")
                            + $styleType->lists('name', 'id')
                            + array('add' => "-- Add new style --");

        $view_content = compact('product', 'selectStyleType');

        $content = View::make('products.new-material.step3', $view_content);
        return $this->formWizard(3, $content);
    }

    public function postStep3()
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 3) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        // edit style type
        if (Input::has("edit_style_type"))
        {
            $styleTypeId = trim(Input::get("style_type_id"));

            $styleType = StyleType::find($styleTypeId);

            if (! $styleType)
            {
                return Redirect::back()->withErrors("Style type not found.");
            }

            if (Input::has("name"))
            {
                $styleType->name = Input::get("name");
            }

            if (Input::has("translate.name"))
            {
                $styleType->setTranslate('name', Input::get('translate.name'));
            }

            if ( ! $styleType->save() )
            {
                return Redirect::back()->withErrors($styleType->errors());
            }

            return Redirect::back()->with('success', 'Style type updated.');
        }

        // create style type and attach
        if (Input::has("create_new_style_type"))
        {
            // get product
            $product = $this->product->with('styleTypes')->findOrFail($productId);

            $styleType = new StyleType;
            $styleType->name = trim(Input::get("new_style_type"));

            if (Input::has("translate.name"))
            {
                $styleType->setTranslate('name', Input::get('translate.name'));
            }

            if ( ! $styleType->save() )
            {
                return Redirect::back()->withErrors($styleType->errors());
            }

            // attach style type to product normally
            $product->styleTypes()->attach($styleType->getKey());

            $product->rebuildStyleTypeMediaSet();

            return Redirect::back()->with('success', 'Style type created.');
        }

        // detach style type
        if (Input::has("detach_style_type"))
        {
            $styleTypeId = intval(Input::get("detach_style_type"));

            $product = $this->product->findOrFail($productId);

            $product->styleTypes()->detach($styleTypeId);

            $product->rebuildStyleTypeMediaSet();

            return Redirect::back()->with('success', 'Style type unlinked.');
        }

        // attach style to page
        if (Input::has('selectType'))
        {
            $materialsId = $this->storageGet('materials_id') ?: array();

            // filter select type
            $selectedStyleType = Input::get('selectType') ?: array();
            $selectedStyleType = array_filter($selectedStyleType);
            $selectedStyleType = array_unique($selectedStyleType);

            // get product with current style type
            $product = $this->product->with('styleTypes', 'variants')->findOrFail($productId);

            if (count($selectedStyleType) > 0)
            {
                //attach remaining to product
                $product->styleTypes()->attach($selectedStyleType);
            }

            $product->rebuildStyleTypeMediaSet();

            // reload style type and variants again
            $product->load('styleTypes', 'variants');

            // get current variants that belongs to this product
            $totalPossibleVariants = $product->variants->count() + count($materialsId);

            if ($product->styleTypes->count() < 1 && $totalPossibleVariants > 1 )
            {
                return Redirect::back()->withErrors("Product must have least 1 style type when product has more than 1 variant in it.");
            }

            ## pre-create style options from materials ##
            // it will use later as variant's style option

            $materialsId = $this->storageGet('materials_id');
            if ($materialsId)
            {
                $materials = ImportedMaterial::whereIn("id", $materialsId)->get();

                // first check 3 core style type
                // for check which one that product want

                $color = $this->getStyleTypeColor();
                $size = $this->getStyleTypeSize();
                $surface = $this->getStyleTypeSurface();

                $productStyleTypeRequired = array();
                foreach ($product->styleTypes as $key => $styleType) {
                    if ($color && $styleType->getKey() == $color->getKey())
                    {
                        $productStyleTypeRequired['color'] = true;
                    }
                    if ($size && $styleType->getKey() == $size->getKey())
                    {
                        $productStyleTypeRequired['size'] = true;
                    }
                    if ($surface && $styleType->getKey() == $surface->getKey())
                    {
                        $productStyleTypeRequired['surface'] = true;
                    }
                }

                $getExistsStyleOption = function($type, $text) use ($color, $size, $surface)
                {
                    return StyleOption::whereStyleTypeId(${$type}->getKey())
                            ->whereText($text)->first();
                };

                $createStyleOption = function($type, $text) use ($color, $size, $surface)
                {
                    $styleOption = new StyleOption;
                    $styleOption->style_type_id = ${$type}->getKey();
                    $styleOption->text = $text;
                    $styleOption->meta = json_encode(array());
                    $styleOption->save();

                    return $styleOption;
                };

                // check each materials for style option value
                // if productStyleTypeRequired has that style type,
                // first we will search in current style options
                // if we can't find them,
                // we will use value to create as new style option
                foreach ($materials as $key => $material) {
                    if (isset($productStyleTypeRequired['color']) && $material->color)
                    {
                        $styleOption = $getExistsStyleOption('color', $material->color);
                        if (! $styleOption)
                        {
                            $createStyleOption('color', $material->color);
                        }
                    }
                    if (isset($productStyleTypeRequired['size']) && $material->size)
                    {
                        $styleOption = $getExistsStyleOption('size', $material->size);
                        if (! $styleOption)
                        {
                            $createStyleOption('size', $material->size);
                        }
                    }
                    if (isset($productStyleTypeRequired['surface']) && $material->surface)
                    {
                        $styleOption = $getExistsStyleOption('surface', $material->surface);
                        if (! $styleOption)
                        {
                            $createStyleOption('surface', $material->surface);
                        }
                    }
                }
            }

            $this->storagePut('state', 4);

            return Redirect::action('ProductNewMaterialController@getStep4');
        }

        return Redirect::back()->with('warning', 'Please try again.');
    }


    public function getStep4()
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $materialsId = $this->storageGet('materials_id');
        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 4 || ! $productId) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process. (4)');
        }

        // check product id exists?
        $product = $this->product->with('brand', 'styleTypes.styleOptions', 'variants.variantStyleOption.styleOption', 'styleOptions')->find($productId);

        // get material
        $materials = $materialsId ? ImportedMaterial::whereIn('id', $materialsId)->get() : array();

        ## Select style option for materials

        // first check 3 core style type
        // for check which one that product want

        $color = $this->getStyleTypeColor();
        $size = $this->getStyleTypeSize();
        $surface = $this->getStyleTypeSurface();

        $productStyleTypeRequired = array();
        foreach ($product->styleTypes as $key => $styleType) {
            if ($styleType->getKey() == $color->getKey())
            {
                $productStyleTypeRequired['color'] = true;
                $styleType->core = 'color';
            }
            if ($styleType->getKey() == $size->getKey())
            {
                $productStyleTypeRequired['size'] = true;
                $styleType->core = 'size';
            }
            if ($styleType->getKey() == $surface->getKey())
            {
                $productStyleTypeRequired['surface'] = true;
                $styleType->core = 'surface';
            }
        }

        $getExistsStyleOption = function($type, $text) use ($color, $size, $surface)
        {
            return StyleOption::whereStyleTypeId(${$type}->getKey())
                    ->whereText($text)->first();
        };

        // set style option id to material for show in view
        foreach ($materials as $key => $material) {
            if (isset($productStyleTypeRequired['color']) && $material->color)
            {
                $styleOption = $getExistsStyleOption('color', $material->color);
                if ($styleOption)
                {
                    $material->color_id = $styleOption->getKey();
                }
            }
            if (isset($productStyleTypeRequired['size']) && $material->size)
            {
                $styleOption = $getExistsStyleOption('size', $material->size);
                if ($styleOption)
                {
                    $material->size_id = $styleOption->getKey();
                }
            }
            if (isset($productStyleTypeRequired['surface']) && $material->surface)
            {
                $styleOption = $getExistsStyleOption('surface', $material->surface);
                if ($styleOption)
                {
                    $material->surface_id = $styleOption->getKey();
                }
            }
        }


        // get all style option
        $selectListStyleOptions = array();
        foreach($product->styleTypes as $id => $styleType)
        {
            $optionList = array();

            foreach ($styleType->styleOptions as $index => $styleOption) {

                $productStyleOption = $product->styleOptions->filter(function($item) use ($styleOption) {
                    return $item->getKey() == $styleOption->getKey() ? $item : false;
                })->first();

                if ($productStyleOption)
                {
                    $styleOption = $productStyleOption;
                    $styleOption->text = $styleOption->pivot->text;
                    $styleOption->meta = $styleOption->pivot->meta;
                }

                if (! is_array($styleOption->meta))
                {
                    $meta = json_decode($styleOption->meta, true);
                    $image = $styleOption->image;
                    if ($image)
                    {
                        $meta['value'] = $image;
                    }

                    $styleOption->meta = $meta;
                }

                $attributes = array(
                    'data-iframe' => URL::action("ProductNewMaterialController@getEditProductStyleOption", array($product->getKey(), $styleType->getKey(), $styleOption->getKey())),
                    'data-json' => $styleOption->toJson(),
                    'text' => $styleOption->text
                );

                $optionList[$styleOption->getKey()] = $attributes;
            }


            $selectListStyleOptions[$styleType->getKey()] = $optionList;
        }

        // underscore js
        $this->theme->asset()->container('macro')->add('underscorejs', 'vendor/underscorejs/underscore-min.js');
        $this->theme->asset()->container('macro')->usePath()->add('jqueryui_autocomplate_combobox', 'admin/js/jqueryui.autocomplete.combobox.js', array('jquery'));

        $this->theme->asset()->container('macro')->usePath()->add('product_new_material_step4', 'admin/js/product_new_material_step4.js', array('jqueryui_combobox_event_var'));

        $view_content = compact('materials', 'product',  'selectListStyleOptions');

        $this->theme->asset()->usePath()->add('wizard', 'custom-plugins/wizard/wizard.css', array('colorpicker'));

        $content = View::make('products.new-material.step4', $view_content);
        return $this->formWizard(4, $content );
    }

    public function postStep4()
    {

        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $materialsId = $this->storageGet('materials_id');
        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 4 || ! $productId) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        if(Input::has("refresh"))
        {
            return Redirect::back()->withInput();
        }

        // find product and load relation
        $product = $this->product->findOrFail($productId);
        $product->load("styleTypes.styleOptions", "variants.variantStyleOptions");

        $errors = array();

        $variantOptions  = (array) Input::get("variant_option");
        $materialOptions = (array) Input::get("material_option");

        $allOptions = $variantOptions + $materialOptions;

        // create stack for store hash from options
        $optionHashStack = array();

        $totalPossibleVariants = $product->variants->count() + count($materialsId);

        if ( count($allOptions) < 1 && $product->styleTypes->count() > 0)
        {
            $errors[5] = "Please select product's attributes for variant/material.";
        }

        foreach ($allOptions as $mixedId => $option)
        {
            foreach ($product->styleTypes as $index => $styleType)
            {
                // check value
                // validate - required
                if (empty($option[$styleType->getKey()]))
                {
                    $errors[5] = "Please select product's attributes for variant/material.";
                }
                else
                {
                    // try to get style options from style type - protect select style option that crossing inject
                    // validate - exists in table
                    $testGetOption = $styleType->styleOptions->find($option[$styleType->getKey()]);
                    if (! $testGetOption)
                    {
                        $errors[6] = "You select style options that don't exists. Try again.";
                    }
                }
            }

            // check duplicate
            // validate - unique in array
            $hash = md5(implode("-", (array) $option));
            if(in_array($hash, $optionHashStack))
            {
                // found duplicate
                $errors[10] = "You can't select set of product's attributes same on 2 variants.";
            }
            // add hash to stack for next loop checking
            $optionHashStack[] = $hash;
        }

        if ($errors)
        {
            return Redirect::back()->withInput()->withErrors($errors);
        }

        /* Manage variants - it mean all variant so it mustn't be under manage material. */

        if($variantOptions) {
            foreach($product->variants as $productVariant) {

                $options = @$variantOptions[$productVariant->getKey()];

                // don't have option for product variant - skip it.
                if (! $options)
                {
                    continue;
                }

                // sync style option id to variants
                $syncList = array();
                foreach ($options as $styleTypeId => $styleOptionId) {
                    $syncList[$styleOptionId] = array("style_type_id" => $styleTypeId);
                }

                $productVariant->styleOptions()->sync($syncList);
            }
        }


        /* Manage materials */

        $newVariantsId = array();

        if($materialsId)
        {
            $materials = ImportedMaterial::whereIn("id", $materialsId)->get();

            foreach ($materials as $material) {

                // set material to variant under product
                $productVariant = new ProductVariant;
                $productVariant->product_id = $product->getKey();
                $productVariant->title = $material->name;
                $productVariant->retail_normal_price = $material->normal_price;
                $productVariant->retail_price = $material->cost_rtp;
                $productVariant->vendor_id = $material->id_vendor;
                $same = array('inventory_id', 'unit_type', 'stock_type', 'shop_id', 'master_id', 'material_code');
                foreach ($same as $field) {
                    $productVariant->{$field} = $material->{$field};
                }

                // set price from SupplyChain
                if ($material->cost_rtp != $material->normal_price)
                {
                    $productVariant->normal_price = $material->normal_price;
                    $productVariant->price = $material->cost_rtp;
                }
                else
                {
                    $productVariant->price = $material->cost_rtp;
                }


                if ( ! $productVariant->save())
                {
                    return Redirect::action('ProductNewMaterialController@getIndex')->withErrors($productVariant->errors());
                }

                $options = @$materialOptions[$material->getKey()];

                if ($options)
                {
                    // sync style option id to variants
                    $syncList = array();

                    foreach ($options as $styleTypeId => $styleOptionId)
                    {
                        $syncList[$styleOptionId] = array("style_type_id" => $styleTypeId);
                    }

                    if (count($syncList) > 0)
                    {
                        $productVariant->styleOptions()->sync($syncList);
                    }
                }

                // lastly - set this material have variant
                $material->variant_id = $productVariant->getKey();
                $material->save();

                // get variant id to stack
                $newVariantsId[] = $productVariant->getKey();
            }
        }

        $this->storagePut('newVariantsId', $newVariantsId);

        /* Manage product */

        // load variants again
        $product->load('variants');

        $totalVariants = $product->variants->count();

        // set has_variant
        $product->has_variants = ($totalVariants > 1) ? 1 : 0;
        if ($product->status == 'incomplete')
        {
            $product->status = 'draft';
        }
        $product->save();

        // restore product
        $product->restore();

        // lasted... update style option to product
        $product = Product::with("variants.styleOptions", "styleOptions")->find($product->getKey());

        $variantStyleOptions = new Illuminate\Database\Eloquent\Collection;
        foreach ($product->variants as $variant)
        {
            foreach ($variant->styleOptions as $key => $styleOption) {
                $id = $styleOption->getKey();
                if ($variantStyleOptions->find($id))
                    continue;

                $variantStyleOptions->add($styleOption);
            }
        }

        $variantStyleOptionsID = $variantStyleOptions->lists('id');

        $current = $product->styleOptions->lists('id');

        // check style option from variant that don't have on product style option
        // create it from template
        foreach ($variantStyleOptionsID as $key => $id) {
            if (! in_array($id, $current))
            {
                // this is neww!!! - get text and meta of style option
                // and set it as default

                $styleOption = StyleOption::find($id);
                if ($styleOption)
                {
                    $product->styleOptions()->attach($styleOption->id, array(
                        'text' => $styleOption->text,
                        'meta' => $styleOption->meta,
                    ));
                }

            }
        }

        // check style option from product style option
        // that don't have on variant so delete it
        foreach ($product->styleOptions as $key => $styleOptions) {
            if (! in_array($styleOptions->id, $variantStyleOptionsID))
            {
                $product->styleOptions()->detach($styleOptions->id);
            }
        }

        // rebuild all variant title
        $product->rebuildVariantsTitle();

        // $product->load('styleOptions');

        // // get style option for create title of variants
        // foreach ($product->variants as $variant)
        // {
        //     $styleOptionText = array();

        //     $styleOptionId = $variant->styleOptions->lists('id');

        //     $product->styleOptions->each(function($styleOption) use(&$styleOptionText, $styleOptionId)
        //     {
        //         if (in_array($styleOption->id, $styleOptionId))
        //         {
        //             $styleOptionText[] = $styleOption->pivot->text;
        //         }
        //     });

        //     $variant->title = $product->title.(count($styleOptionText) ? ' ('.implode(', ', $styleOptionText).')' : '');
        //     $variant->save();
        // }

        $this->storagePut('state', 5);

        return Redirect::action('ProductNewMaterialController@getSummary');
    }

    public function getCreateStyleOption($styleTypeId)
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        // check user use follow process
        if($state < 4) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        $styleType = StyleType::with("styleOptions")->findOrFail($styleTypeId);

        $styleOption = null;

        $view = compact("styleType", "styleOption");

        $this->theme->layout("dialog");

        return $this->theme->of("products.new-material.form-style-option", $view)->render();
    }

    public function postCreateStyleOption($styleTypeId)
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        // check user use follow process
        if($state < 4) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        $errors = $this->validateStyleOption();

        if ($errors)
        {
            return $errors;
        }

        $styleType = StyleType::findOrFail($styleTypeId);

        $styleOption = new StyleOption;
        $styleOption->style_type_id = $styleType->getKey();

        // save style option
        $styleOption = $this->saveStyleOption($styleOption);

        return $this->resposeRefreshParent();
    }

    public function getEditStyleOption($styleTypeId, $styleOptionId)
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        // check user use follow process
        if($state < 4) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        $styleType = StyleType::with("styleOptions")->findOrFail($styleTypeId);

        $styleOption = $styleType->styleOptions->find($styleOptionId);

        if (! $styleOption)
        {
            return Redirect::action('ProductNewMaterialController@getStep4')->with('warning', 'Style option don\'t exists.');
        }

        $styleOption->meta = json_decode($styleOption->meta, true);

        $view = compact("styleType", "styleOption");

        $this->theme->layout("dialog");

        return $this->theme->of("products.new-material.form-style-option", $view)->render();
    }

    public function postEditStyleOption($styleTypeId, $styleOptionId)
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        // check user use follow process
        if($state < 4) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        $styleType = StyleType::with("styleOptions")->findOrFail($styleTypeId);

        $styleOption = $styleType->styleOptions->find($styleOptionId);

        if (! $styleOption)
        {
            return Redirect::action('ProductNewMaterialController@getStep4')->with('warning', 'Style option don\'t exists.');
        }

        $errors = $this->validateStyleOption($styleOption);

        if ($errors)
        {
            return $errors;
        }


        // save style option
        $styleOption = $this->saveStyleOption($styleOption);

        return $this->resposeRefreshParent();
    }

    public function getEditProductStyleOption($productId, $styleTypeId, $styleOptionId)
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        // check user use follow process
        if($state < 4) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }

        // prepare query of product
        $queryStyleTypes = function($query) use ($styleTypeId)
        {
            $table = $query->getModel()->getTable();
            $primaryKey = $query->getModel()->getKeyName();
            return $query->where("{$table}.{$primaryKey}", "=", $styleTypeId);
        };

        $queryStyleOptions = function($query) use ($styleOptionId)
        {
            $table = $query->getModel()->getTable();
            $primaryKey = $query->getModel()->getKeyName();
            return $query->where("{$table}.{$primaryKey}", "=", $styleOptionId);
        };

        $relations = array(
            "styleTypes" => $queryStyleTypes,
            "styleTypes.styleOptions" => $queryStyleOptions,
            "styleOptions" => $queryStyleOptions
            );

        // get product
        $product = $this->product->with($relations)->findOrFail($productId);

        $styleType = $product->styleTypes->first();

        if (! $styleType)
        {
            return App::abort(404);
        }

        $styleOptionMaster = $styleType->styleOptions->first();

        if (! $styleOptionMaster)
        {
            return App::abort(404);
        }

        // try to get style option that belong to product
        $filterStyleOption = function($item) use ($styleOptionId)
        {
            return $item->pivot->style_option_id == $styleOptionId ? $item : false;
        };
        $productStyleOption = $product->styleOptions->filter($filterStyleOption)->first();

        if ($productStyleOption)
        {
            // found style option so get pivot model to form
            $styleOption = ProductStyleOption::find($productStyleOption->pivot->getKey());
        }
        else
        {
            // get normal style option
            $styleOption = $styleType->styleOptions->find($styleOptionId);
        }

        // last catch -  style option should exists
        if (! $styleOption)
        {
            return Redirect::action('ProductNewMaterialController@getStep4')->with('warning', 'Style option don\'t exists.');
        }

        $styleOption->meta = json_decode($styleOption->meta, true);

        $view = compact("product", "styleType", "styleOption");

        $this->theme->layout("dialog");

        return $this->theme->of("products.new-material.form-product-style-option", $view)->render();
    }

    public function postEditProductStyleOption($productId, $styleTypeId, $styleOptionId)
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        // check user use follow process
        if($state < 4) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process.');
        }


        // prepare query of product
        $queryStyleTypes = function($query) use ($styleTypeId)
        {
            $table = $query->getModel()->getTable();
            $primaryKey = $query->getModel()->getKeyName();
            return $query->where("{$table}.{$primaryKey}", "=", $styleTypeId);
        };

        $queryStyleOptions = function($query) use ($styleOptionId)
        {
            $table = $query->getModel()->getTable();
            $primaryKey = $query->getModel()->getKeyName();
            return $query->where("{$table}.{$primaryKey}", "=", $styleOptionId);
        };

        $relations = array(
            "styleTypes" => $queryStyleTypes,
            "styleTypes.styleOptions" => $queryStyleOptions,
            "styleOptions" => $queryStyleOptions
            );

        // get product
        $product = $this->product->with($relations)->findOrFail($productId);

        $styleType = $product->styleTypes->first();

        if (! $styleType)
        {
            return App::abort(404);
        }

        $styleOptionMaster = $styleType->styleOptions->first();

        if (! $styleOptionMaster)
        {
            return App::abort(404);
        }

        // try to get style option that belong to product
        $filterStyleOption = function($item) use ($styleOptionId)
        {
            return $item->pivot->style_option_id == $styleOptionId ? $item : false;
        };
        $productStyleOption = $product->styleOptions->filter($filterStyleOption)->first();

        if ($productStyleOption)
        {
            // found productStlyeOption so this will be edit case.
            $productStyleOption = ProductStyleOption::find($productStyleOption->pivot->getKey());

            $found = true;
        }
        else
        {
            // not found so it will create
            $productStyleOption = new ProductStyleOption;
            $productStyleOption->product_id = $product->getKey();
            $productStyleOption->style_option_id = $styleOptionId;

            $found = false;
        }


        $styleOptionForValidate = $found ? $productStyleOption : $styleOptionMaster;
        $errors = $this->validateStyleOption($styleOptionForValidate);

        if ($errors)
        {
            return $errors;
        }

        // when user submit for create new product style option
        // user allow to not upload image when master has image
        if (Input::get("meta_type") == 'image')
        {
            // get product style option meta as array to $meta
            if (! is_array($productStyleOption->meta))
            {
                $meta = json_decode($productStyleOption->meta, true);
            }
            else
            {
                $meta = $productStyleOption->meta;
            }


            if (! array_get($meta, 'value'))
            {
                // yes.. product style option don't has image

                // try to get image path from their master
                $meta = json_decode($styleOptionMaster->meta, true);
                $masterMetaValue = array_get($meta, 'value');

                // found image
                if ($masterMetaValue)
                {
                    // set value from master
                    $meta['value'] = $masterMetaValue;

                    $productStyleOption->meta = $meta;
                }
            }
        }

        // save product style option
        $productStyleOption = $this->saveStyleOption($productStyleOption);

        // save to master when meta of master is blank.
        $meta = json_decode($styleOptionMaster->meta, true);
        if ($meta == false) // array blank is equal false.
        {
            $styleOptionMaster = $this->saveStyleOption($styleOptionMaster);
        }

        return $this->resposeRefreshParent();
    }

    protected function resposeRefreshParent()
    {
        $this->theme->asset()->container('embed')->writeScript('callback', '
            $(function() {
                // window.parent.location.reload();
                window.parent.$("#refresh").trigger("click");
                window.parent.$.fancybox.close();
            })
        ');

        $this->theme->layout("dialog");

        return $this->theme->string('')->render();
    }

    protected function saveStyleOption($styleOption)
    {
        if (! is_array($styleOption->meta))
        {
            $meta = json_decode($styleOption->meta, true);
        }
        else
        {
            $meta = $styleOption->meta;
        }

        if (! is_array($meta))
        {
            $meta = array();
        }

        $type = Input::get("meta_type");
        $meta['type'] = $type;
        switch ($type) {
            case 'color':
                $meta['value'] = Input::get("meta_color");
                break;

            case 'text':
                $meta['value'] = Input::get("meta_text");
                break;

            case 'image':
                $image = Input::file("meta_image");
                if ($image)
                {
                    $width  = 50;
                    $height = 50;

                    $styleOptionID = $styleOption->style_option_id ?: $styleOption->getKey();

                    $writePath = Config::get('up::uploader.baseDir');

                    // create upload path
                    $fileName = time().($styleOption->product_id ? '-product-'.$styleOption->product_id : '').".jpg";
                    $uploadDir = "style_options/{$styleOptionID}";
                    $directory = rtrim($writePath, "/").DIRECTORY_SEPARATOR.$uploadDir;


                    if (!is_dir($directory)) {
                        if (false === @mkdir($directory, 0777, true)) {
                            throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
                        }
                    } elseif (!is_writable($directory)) {
                        throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
                    }

                    $uploadPath = $directory.DIRECTORY_SEPARATOR.$fileName;

                    // upload image
                    WideImage::load($image->getPathName())
                        ->resize($width, $height, 'outside')
                        ->crop('center', 'middle', $width, $height)
                        ->saveToFile($uploadPath);

                    $meta['value'] = $uploadDir.'/'.$fileName;
                }

                break;
        }

        $styleOption->text = Input::get("text");
        $styleOption->meta = json_encode($meta);
        $styleOption->setTranslate('text', Input::get('translate.text'));

        $styleOption->save();

        return $styleOption;
    }

    protected function validateStyleOption($styleOption = null)
    {
        $rules = array(
            'text' => 'required',
            'meta_type' => 'required|in:color,text,image',
            'meta_color' => 'required_if:meta_type,color',
            'meta_image' => 'required_if:meta_type,image|image',
            'meta_text' => 'required_if:meta_type,text'
            );

        if ($styleOption)
        {
            if ($styleOption->image)
            {
                unset($rules['meta_image']);
            }
        }

        $messages = array(
            'meta_type.required' => 'Style option type is required.',
            'meta_color.required_if' => 'Color is required.',
            'meta_image.required_if' => 'Image is required.',
            'meta_text.required_if' => 'Text is required.',
            );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        return false;
    }

    public function getSummary()
    {
        // get inv id when click step back or recover from closed browser
        $state = $this->storageGet('state', 1);

        $newVariantsId = $this->storageGet('newVariantsId');

        $materialsId = (array) $this->storageGet('materials_id');
        $productId = $this->storageGet('product_id');

        // check user use follow process
        if($state < 5 || ! $productId) {
            return Redirect::action('ProductNewMaterialController@getIndex')->with('warning', 'You cannot skip process. (5)');
        }

        // get product with all variants
        $product = Product::with('brand', 'styleTypes', 'variants.variantStyleOptions.styleOption')->find($productId);

        $view_content = compact('product', 'materialsId', 'newVariantsId');

        // $this->storagePut('state', 1);

        $content = View::make('products.new-material.summary', $view_content);
        return $this->formWizard(5, $content, true);
    }

    protected function formWizard($step, $content, $disableStack = false)
    {
        $this->theme->asset()->usePath()->add('wizard', 'custom-plugins/wizard/wizard.css', array('colorpicker'));

        $this->theme->prependTitle("{$this->step[$step]['label']} - New material");
        $view = array(
            'all_step' => $this->step,
            'step' => $step,
            'content' => $content
        );

        $view['disableStack'] = $disableStack;

        return $this->theme->of('products.new-material.form-wizard', $view)->render();
    }

    protected function storageGet($key, $default = null)
    {
        return Session::get("{$this->prefixSession}.{$key}", $default);
    }

    protected function storagePut($key, $value)
    {
        return Session::put("{$this->prefixSession}.{$key}", $value);
    }

    protected function storageForget($key)
    {
        return Session::forget("{$this->prefixSession}.{$key}");
    }

    protected function storageDestroy()
    {
        return Session::forget("{$this->prefixSession}");
    }

    protected function getStyleTypeColor()
    {
        return StyleType::remember(5)->find(1);
    }

    protected function getStyleTypeSize()
    {
        return StyleType::remember(5)->find(2);
    }

    protected function getStyleTypeSurface()
    {
        return StyleType::remember(5)->find(3);
    }

/*
    protected function isReferer($match)
    {
        // check referer from step1
        $header = Request::header();
        $base = str_replace('/index', '', URL::action('ProductNewMaterialController@getIndex'));
        $referer = str_replace($base, '', $header['referer'][0]);

        return strpos($referer, $match) !== false;
    }
*/
}