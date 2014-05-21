<?php

class ProductRepository implements ProductRepositoryInterface {

    protected $product;

    public function __construct($product=null)
    {
        if($product==false)
            $this->product = new Product();
        else
            $this->product = $product;
    }

    public function getProductByPkey($pkey)
    {
        // Visible Fields.
        $visibleProductFields = array('id', 'pkey', 'title', 'slug', 'description', 'key_feature', 'brand', 'collections', 'installment', 'has_variants', 'variants', 'mediaContents', 'tag', 'policies', 'price_range', 'net_price_range', 'special_price_range', 'percent_discount', 'published_at', 'created_at', 'updated_at', 'image_cover', 'translate', 'metas', 'allow_cod', 'discount_ended');
        $visibleBrandFields   = array('pkey', 'name', 'slug', 'thumbnail');
        $visibleVariantFields = array('pkey', 'inventory_id', 'title', 'normal_price', 'price', 'unit_type', 'mediaContents', 'net_price', 'special_price', 'installment', 'style_options', 'active_special_discount', 'active_trueyou_discount');
        $visibleMediaContentsFields = array('mode', 'url', 'thumb');
        $visibleCollectionsFields   = array('pkey', 'name', 'is_category');

        // Load Relations.
        $loadArr = array('brand', 'collections', 'styleTypes', 'mediaContents', 'variants', 'variants.mediaContents', 'variants.variantStyleOption', 'variants.variantStyleOption.styleType', 'variants.activeSpecialDiscount');

        // Get Product
        $product = Product::with($loadArr)->where('pkey', $pkey)->where('status', 'publish')->first();

        // If Empty Product, Return 404 Response.
        if (empty($product))
        {
            return FALSE;
        }

        // Set Appends and Set Visible fields for product and brand.
        $product->setAppends(array('metas'));
        $product->setVisible($visibleProductFields);
        $product->discount_ended = '';
        $product->brand->setAppends(array('thumbnail'));
        $product->brand->setVisible($visibleBrandFields);

        // Set Appends and Set Visible fields for collections.
        if ( !$product->collections->isEmpty() )
        {
            $product->collections->each(function($collection) use($visibleCollectionsFields)
            {
                $collection->setVisible($visibleCollectionsFields);
            });
        }

        // Set Appends, Set Visible fields for Media Content.
        // and Set all media content path
        if ( !$product->mediaContents->isEmpty() )
        {
            $product->mediaContents->each(function($mediaContent) use($visibleMediaContentsFields)
            {
                $mediaContent->url = (string) $mediaContent->link;
                $mediaContent->setVisible($visibleMediaContentsFields);
				$mediaContent->thumb = array(
					'normal' => (string) $mediaContent->link,
                    'thumbnails' => array(
                        'small'     => (string) UP::lookup($mediaContent->attachment_id)->scale('s'),
                        'medium'    => (string) UP::lookup($mediaContent->attachment_id)->scale('m'),
                        'square'    => (string) UP::lookup($mediaContent->attachment_id)->scale('square'),
                        'large'     => (string) UP::lookup($mediaContent->attachment_id)->scale('l'),
                        'zoom'      => (string) UP::lookup($mediaContent->attachment_id)->scale('xl')
                    )
				);
            });

            // Set Image Cover
            $mediaImage = $product->mediaContents()->where('mode', 'image')->first();
            if ( !empty($mediaImage) )
            {
                $product->image_cover = array(
                    'normal' => (string) $mediaImage->link,
                    'thumbnails' => array(
                        'small'     => (string) UP::lookup($mediaImage->attachment_id)->scale('s'),
                        'medium'    => (string) UP::lookup($mediaImage->attachment_id)->scale('m'),
                        'square'    => (string) UP::lookup($mediaImage->attachment_id)->scale('square'),
                        'large'     => (string) UP::lookup($mediaImage->attachment_id)->scale('l'),
                        'zoom'      => (string) UP::lookup($mediaImage->attachment_id)->scale('xl')
                    )
                );
            }
        }

        $styleTypesArr = array();
        $variantStyleOptionsArr = array();
        $uniquePkey = array();

        $product->variants->each(function($variant) use($visibleVariantFields, $visibleMediaContentsFields, $product, &$styleTypesArr, &$variantStyleOptionsArr, &$uniquePkey)
        {
            // Get Variant Options
            if ( !$variant->variantStyleOption->isEmpty() )
            {
                $variant->style_options = array();
                $tmpStyleOptions = array();

                foreach($variant->variantStyleOption as $val)
                {
                    $typeId = $val->style_type_id;

                    if ( !$product->styleTypes->isEmpty() )
                    {
                        $productStyleType = $product->styleTypes()->where('id', $typeId)->first();
                    }
                    else
                    {
                        $productStyleType = null;
                    }

                    if ( !isset($uniquePkey[$val->style_type_id]) )
                    {
                        $uniquePkey[$val->style_type_id] = array();
                    }

                    if ( !in_array($val->pkey, $uniquePkey[$val->style_type_id]) )
                    {
                        $uniquePkey[$val->style_type_id][] = $val->pkey;

                        $optionData = array(
                            'text' => $val->text,
                            'meta' => json_decode($val->meta),
                            'pkey' => $val->pkey,
                        );

                        // if ( !empty($productStyleType) && $productStyleType->pivot->media_set == 1)
                        // {
                        //     // $vsoMc, it means "VariantStyleOption MediaContent"
                        //     // $vsoMc = $val->mediaContents()->where('mode', 'image')->get();
                        //     $vsoMc = $val->mediaContents;

                        //     if ( !$vsoMc->isEmpty() )
                        //     {
                        //         $mcArr = array();

                        //         foreach ($vsoMc as $mc)
                        //         {
                        //             // $mcArr[] = (string) $mc->image;
                        //             $mcArr[] = array(
                        //                 'mode' => $mc->mode,
                        //                 'url'  => (string) $mc->image,
                        //                 'thumb' => array(
                        //                     'normal' => (string) $mc->image,
                        //                     'thumbnails' => array(
                        //                         'small'     => (string) UP::lookup($mc->attachment_id)->scale('s'),
                        //                         'medium'    => (string) UP::lookup($mc->attachment_id)->scale('m'),
                        //                         'square'    => (string) UP::lookup($mc->attachment_id)->scale('square'),
                        //                         'large'     => (string) UP::lookup($mc->attachment_id)->scale('l'),
                        //                         'zoom'      => (string) UP::lookup($mc->attachment_id)->scale('xl')
                        //                     )
                        //                 )
                        //             );
                        //         }

                        //         $optionData['media_contents'] = $mcArr;
                        //     }
                        // }

                        $styleTypesArr[$typeId][] = $optionData;
                    }

                    $tmpStyleOptions[] = array(
                        'style' => $val->styleType->pkey,
                        'option' => $val->pkey
                    );

                    // // $vsoMc, it means "VariantStyleOption MediaContent"
                    // $vsoMc = $val->mediaContents()->where('mode', 'image')->get();

                    // if ( !$vsoMc->isEmpty() )
                    // {
                    //     // "VariantStyleOption MediaContent" Array
                    //     $mcArr = array();
                    //     foreach ($vsoMc as $mc)
                    //     {
                    //         $mcArr[] = (string) $mc->image;
                    //     }

                    //     $tmpStyleOptions[] = array(
                    //         'style' => $val->styleType->pkey,
                    //         'option' => $val->pkey,
                    //         'media_contents' => $mcArr
                    //     );
                    // }
                    // else
                    // {
                    //     $tmpStyleOptions[] = array(
                    //         'style' => $val->styleType->pkey,
                    //         'option' => $val->pkey
                    //     );
                    // }
                }

                $variant->style_options = $tmpStyleOptions;
            }

            // Get Variant Media Content
            if ( !$variant->mediaContents->isEmpty() )
            {
                foreach ( $variant->mediaContents as $variant_mediaContent)
                {
                    $variant_mediaContent->url = (string) $variant_mediaContent->link;
                    $variant_mediaContent->setVisible($visibleMediaContentsFields);
                }
            }

            // Set Net Price and Special Price
            // Invoke Accessor Function (in Model ProductVariant).
            $variant->setAppends(array('net_price', 'special_price', 'active_trueyou_discount'));
            // $variant->net_price = $variant->net_price;
            // $variant->special_price = $variant->special_price;

            /*
            if ($variant->price != 0 && $variant->normal_price == 0)
            {
                $variant->net_price = $variant->price;
                $variant->special_price = $variant->normal_price;
            }
            else
            {
                $variant->net_price = $variant->normal_price;
                $variant->special_price = $variant->price;
            }
            */

            // Get Active Special Discount
            if ( !$variant->activeSpecialDiscount )
            {
                $variant->active_special_discount = array();
            }
            else
            {
                $variant->active_special_discount = array_only($variant->activeSpecialDiscount->toArray(), array('campaign_type', 'discount_price', 'discount', 'discount_type', 'started_at', 'ended_at'));

                if ($product->discount_ended == '')
                {
                    $product->discount_ended = $variant->activeSpecialDiscount->ended_at;
                }
                else
                {
                    if ($product->discount_ended < $variant->activeSpecialDiscount->ended_at)
                    {
                        $product->discount_ended = $variant->activeSpecialDiscount->ended_at;
                    }
                }
            }

            // Set Visible Fields
            $variant->setVisible($visibleVariantFields);
        });

        // set Price Range of product
        $priceMax = 0;
        $netPriceMax = 0;
        $specialPriceMax = 0;
        $dcMax = 0;

        $variants = $product->variants()->with('activeSpecialDiscount')->get();

        foreach ($variants as $variant)
        {
            $priceMax = ($priceMax < $variant->price) ? $variant->price : $priceMax ;
            $netPriceMax = ($netPriceMax < $variant->net_price) ? $variant->net_price : $netPriceMax ;
            $specialPriceMax = ($specialPriceMax < $variant->special_price) ? $variant->special_price : $specialPriceMax ;
            $dcMax = ($dcMax < $variant->percent_discount) ? $variant->percent_discount : $dcMax ;
        }

        $priceMin = $priceMax;
        $netPriceMin = $netPriceMax;
        $specialPriceMin = $specialPriceMax;
        $dcMin = $dcMax;

        foreach ($variants as $variant)
        {
            $priceMin = ($priceMin > $variant->price) ? $variant->price : $priceMin ;
            $netPriceMin = ($netPriceMin > $variant->net_price) ? $variant->net_price : $netPriceMin ;
            $specialPriceMin = ($specialPriceMin > $variant->special_price) ? $variant->special_price : $specialPriceMin ;
            $dcMin = ($dcMin > $variant->percent_discount) ? $variant->percent_discount : $dcMin ;
        }

        // Force type force by Tee++;
        $product->price_range = array(
            'max' => $priceMax,
            'min' => $priceMin
        );

        $product->net_price_range = array(
            'max' => $netPriceMax,
            'min' => $netPriceMin
        );

        $product->special_price_range = array(
            'max' => (float) $specialPriceMax,
            'min' => (float) $specialPriceMin
        );

        $product->percent_discount = array(
            'max' => $dcMax,
            'min' => $dcMin
        );

        // Get Translate
        $translate = $product->translate();
        if (!empty($translate))
        {
            $product->translate = array_except($product->translate('en_US')->toArray(),  array('id'));
        }
        else
        {
            $product->translate = null;
        }

        $productArr = $product->toArray();

        // Get Product Policy
        // $productArr['policies'] = $this->getPolicies($product);
        $productArr['policies'] = array();

        // Get Variant Style
        $productArr['style_types'] = NULL;



        if ( !$product->styleTypes->isEmpty() )
        {
            // $productArr['style_types'] = $product->styleTypes->lists('name', 'name');

            // $styles = $product->styleTypes->toArray();
            $styleTypes = $product->styleTypes;
            $productStyleOptions = $product->styleOptions;

            $variantAllStyleOptionId = array();
            foreach ($product->variants as $key => $variant) {
                foreach($variant->variantStyleOptions as $variantStyleOption)
                {
                    $variantAllStyleOptionId[] = $variantStyleOption->style_option_id;
                }
            }

            foreach ($styleTypes as $styleType)
            {
                $filtered = $productStyleOptions->filter(function($model) use ($styleType)
                {
                    return ( $model->style_type_id == $styleType->id );
                });

                $options = array();
                foreach ($filtered as $val)
                {
                    if (! in_array($val->pivot->style_option_id, $variantAllStyleOptionId))
                    {
                        continue;
                    }

                    // table product_style_option ... is pivot table for Product and StyleOption
                    // And It has ProductStyleOption Model too .....
                    // $pso means 'productStyleOption'
                    $pso = ProductStyleOption::find($val->pivot->id);

                    // $psoMc means 'productStyleOption MediaContent'
                    $psoMc = $pso->mediaContents;

                    if ( !$psoMc->isEmpty() )
                    {
                        $mcArr = array();

                        foreach ($psoMc as $mc)
                        {
                            $mcArr[] = array(
                                'mode' => $mc->mode,
                                'url'  => (string) $mc->image,
                                'thumb' => array(
                                    'normal' => (string) $mc->image,
                                    'thumbnails' => array(
                                        'small'     => (string) UP::lookup($mc->attachment_id)->scale('s'),
                                        'medium'    => (string) UP::lookup($mc->attachment_id)->scale('m'),
                                        'square'    => (string) UP::lookup($mc->attachment_id)->scale('square'),
                                        'large'     => (string) UP::lookup($mc->attachment_id)->scale('l'),
                                        'zoom'      => (string) UP::lookup($mc->attachment_id)->scale('xl')
                                    )
                                )
                            );
                        }

                        $meta = json_decode($pso->meta, TRUE);
                        if (array_get($meta, 'type') == 'image' && isset($meta['value']))
                        {
                            if (! URL::isValidUrl($meta['value']))
                            {
                                $meta['value'] = Config::get('up::uploader.baseUrl').'/'.ltrim($meta['value'], '/');
                            }
                        }

                        $options[] = array(
                            'text'           => $pso->text,
                            'meta'           => $meta,
                            'pkey'           => $val->pkey,
                            'media_contents' => $mcArr
                        );
                    }
                    else
                    {
                        $meta = json_decode($pso->meta, TRUE);
                        if (array_get($meta, 'type') == 'image' && isset($meta['value']))
                        {
                            if (! URL::isValidUrl($meta['value']))
                            {
                                $meta['value'] = Config::get('up::uploader.baseUrl').'/'.ltrim($meta['value'], '/');
                            }
                        }

                        $options[] = array(
                            'text' => $val->text,
                            'meta' => $meta,
                            'pkey' => $val->pkey,
                        );
                    }
                }


                $productArr['style_types'][] = array(
                    'name'      => $styleType->name,
                    'pkey'      => $styleType->pkey,
                    'media_set' => ($styleType->pivot->media_set == 1) ? TRUE : FALSE ,
                    // 'options'   => isset($styleTypesArr[$styleType->id]) ? $styleTypesArr[$styleType->id] : NULL,
                    'options'   => $options
                );

                /*
                $productArr['style_types'][] = array(
                    'name' => $style['name'],
                    'pkey' => $style['pkey'],
                    'options' => isset($styleTypesArr[$style['id']]) ? $styleTypesArr[$style['id']] : NULL,
                );
                */
            }
        }

        foreach ($productArr['variants'] as $key=>$val)
        {
            // Force Unset this field.
            unset($productArr['variants'][$key]['variant_style_option']);
        }

        return $productArr;
    }

    public function getPolicies(Product $product)
    {
        $policies = array();

        // $variant = $product->variants->first();

        // if (empty($variant))
        // {
        //     return $policies;
        // }

        // $vendorId = $variant->vendor_id;

        // $brandId = $product->brand->id;

        // $brandPolicies = DB::table('vendors_policies')->where('vendor_id', $vendorId)->where('brand_id', $brandId)->get();

        // foreach ($brandPolicies as $key=>$val)
        // {
        //     $policies[$val->policy_id] = array(
        //         'status'                => $val->status,
        //         'title'       => $val->policy_title_th,
        //         'description' => $val->policy_description_th,
        //         'translates'  => array(
        //             'en_US' => array(
        //                 'title'          => $val->policy_title,
        //                 'description'    => $val->policy_description,
        //             ),
        //         )

        //     );
        // }

        // $vendorPolicies = DB::table('vendors_policies')->where('vendor_id', $vendorId)->get();

        // foreach ($vendorPolicies as $key=>$val)
        // {
        //     if (isset($policies[$val->policy_id]))
        //     {
        //         continue;
        //     }

        //     $policies[$val->policy_id] = array(
        //         'status'                => $val->status,
        //         'title'       => $val->policy_title_th,
        //         'description' => $val->policy_description_th,
        //         'translates'  => array(
        //             'en_US' => array(
        //                 'title'          => $val->policy_title,
        //                 'description'    => $val->policy_description,
        //             ),
        //         )
        //     );
        // }

        // foreach ($policies as $policyId=>$policy)
        // {
        //     if ($policy['status'] == 'not_used')
        //     {
        //         unset($policies[$policyId]);
        //         continue;
        //     }

        //     // ดึงรูปภาพของ Policy .....
        //     $attachmentId = DB::table('attachment_relates')->where('fileable_type', 'Policy')->where('fileable_id', $policyId)->pluck('attachment_id');

        //     $policies[$policyId]['image'] = (string) UP::lookup($attachmentId);
        // }

        // $policies = array_values($policies);

        $variant = $product->variants->first();

        if (empty($variant))
        {
            return $policies;
        }

        $policyPerModel = Config::get('global.policy_per_model', 3);

        $models = array('Brand', 'VVendor', 'Shop');

        foreach ($models as $modelName)
        {
            switch ($modelName) {
                case 'Brand':
                    if (! $product->brand)
                    {
                        $product->load('brand');
                    }
                    $primaryId = $product->brand->id;
                    break;

                case 'VVendor':
                    if (! $product->variants)
                    {
                        $product->load('variants');
                    }
                    $variant = $product->variants->first();
                    $primaryId = $variant->vendor_id;
                    break;

                case 'Shop':
                    if (! $product->variants)
                    {
                        $product->load('variants');
                    }
                    $variant = $product->variants->first();
                    if (! $variant->vendor)
                    {
                        $variant->load('vendor');
                    }
                    $primaryId = $variant->vendor->shop_id;
                    break;
            }

            $model = $modelName::with('policies')->find($primaryId);

            if (! $model || ! $model->policies->count())
            {
                continue;
            }

            // prepare again before replace tagging
            if (! $product->brand)
            {
                $product->load('brand.translates');
            }
            if (! $product->variants)
            {
                $product->load('variants');
            }
            $variant = $product->variants->first();
            if (! $variant->vendor)
            {
                $variant->load('vendor');

            }
            if (! $variant->vendor->shop)
            {
                $variant->vendor->load('shop');
            }


            foreach ($model->policies as $key => $policy)
            {
                if ($policy->use_type == "no")
                {
                    continue;
                }

                $policyType = Config::get('global.policy_type.'.$policy->type);

                $replace = array(
                    '{shop}' => empty($variant->vendor->shop) ? '{shop}' : $variant->vendor->shop->name,
                    '{vendor}' => empty($variant->vendor->name) ? '{vendor}' : $variant->vendor->name,
                    '{brand}' => empty($product->brand->name) ? '{brand}' : $product->brand->name
                    );

                $policyDescription = strtr($policy->description, $replace);

                $policies[$policy->policy_id] = array(
                    'title'       => $policy->title,
                    'description' => $policyDescription,
                    'translates'  => array(),
                    'type'        => $policyType
                );

                foreach ($policy->translates as $key => $translate) {

                    $translateBrand = $product->brand->translates->filter(function($model) use ($translate) {
                        return ($model->locale == $translate->locale);
                    })->first();

                    $replace = array(
                        '{shop}' => empty($variant->vendor->shop) ? '{shop}' : $variant->vendor->shop->name,
                        '{vendor}' => empty($variant->vendor->name) ? '{vendor}' : $variant->vendor->name,
                        '{brand}' => $translateBrand ? $translateBrand->name : (empty($product->brand->name) ? '{brand}' : $product->brand->name)
                        );
                    $policyDescription = strtr($translate->description, $replace);

                    $policies[$policy->policy_id]['translates'][$translate->locale] = array(
                        'title' => $translate->title,
                        'description' => $policyDescription
                        );
                }

                // if (empty($policies[$policy->policy_id]['translates']['en_US']))
                // {
                //     $policies[$policy->policy_id]['translates']['en_US'] = array(
                //         'title'       => $policy->title,
                //         'description' => $policy->description,
                //         );
                // }
            }

            if (count($policies) > 0)
            {
                break;
            }
        }

        foreach ($policies as $policyId => $policy)
        {
            // ดึงรูปภาพของ Policy .....
            $attachmentId = DB::table('attachment_relates')->where('fileable_type', 'Policy')->where('fileable_id', $policyId)->pluck('attachment_id');

            $policies[$policyId]['image'] = (string) UP::lookup($attachmentId);
        }

        $policies = array_values($policies);

        return $policies;
    }




























    public function find($id)
    {
        return $this->product->findOrFail($id);
    }

    /* Set an Eloquent Builder to get only product that has revisions */
    /* Return this repository object */
    public function hasModified()
    {
        $this->product = $this->product->has('revisions');

        return $this;
    }

    /* Execute query with input that get from Search form, */
    /* And Return an "Eloquent Builder" back (Not a results) */
    public function executeFormSearch()
    {
        $this->product = $this->product->with('collections')
                                       ->hasTitle(Input::get('product'))
                                       ->hasTag(Input::get('tag'))
                                       ->ofBrand(Input::get('brand'))
                                       ->sellsByVendor(Input::get('vendor_id'))
                                       ->hasProductLine(Input::get('product_line'))
                                       ->allowCod(Input::get('product_allow_cod'))
                                       ->isContentExist(Input::get('has_product_content'));
                                       // ->isAllowsInstallment(Input::get('product_allow_installment'));

        return $this->product;
    }

    /* Execute query with input that get from Search form */
    /* And Return a results in a form of an "Eloquent Collection" */
    public function getExecuteFormSearch()
    {
        // Get Products Collections.
        $products = $this->executeFormSearch()->get();

        // Filter Search Results.
        $products = $this->filterSearchResults($products);

        return $products;
    }

    public function filterSearchResults($products)
    {

        // Filter Search Result by not Collection
        $products = $this->filterResultsByCategoryExist($products);

        // Filter Search Results by Product Media Content
        $products = $this->filterResultsByProductMediaContent($products);

        // Filter Search Results by Variant Price
        $products = $this->filterResultsByVariantPrice($products);

        // Filter Search Results by Variant Media Content
        $products = $this->filterResultsByVariantMediaContent($products);

        // Filter Search Results by Variant Installment
        $products = $this->filterResultsByVariantAllowsInstallment($products);

        return $products;
    }

    public function filterResultsByCategoryExist(Illuminate\Database\Eloquent\Collection $productCollection)
    {
        if (Input::has('has_collection'))
        {
            if (Input::get('has_collection') == 'yes' or Input::get('has_collection') == 'no')
            {
                $productCollection = $productCollection->filter(function($product) {

                    $hasCollection = FALSE;

                    if ( ! $product->collections->isEmpty())
                    {
                        $hasCollection = TRUE;
                    }

                    if (Input::get('has_collection') == 'yes')
                        return $hasCollection;
                    elseif (Input::get('has_collection') == 'no')
                        return !$hasCollection;
                });
            }
        }
        return $productCollection;
    }

    public function filterResultsByProductMediaContent(Illuminate\Database\Eloquent\Collection $productCollection)
    {
        if (Input::has('has_product_mediacontent'))
        {
            if (Input::get('has_product_mediacontent') == 'yes' or Input::get('has_product_mediacontent') == 'no')
            {
                $productCollection = $productCollection->filter(function($product) {

                    $hasMediaContent = FALSE;

                    if (!$product->variants->isEmpty())
                    {
                        if (!$product->mediaContents->isEmpty())
                        {
                            $hasMediaContent = TRUE;
                        }
                    }

                    if (Input::get('has_product_mediacontent') == 'yes')
                        return $hasMediaContent;
                    elseif (Input::get('has_product_mediacontent') == 'no')
                        return !$hasMediaContent;
                });
            }
        }

        return $productCollection;
    }

    public function filterResultsByVariantPrice(Illuminate\Database\Eloquent\Collection $productCollection)
    {
        if (Input::has('has_price'))
        {
            if (Input::get('has_price') == 'yes' or Input::get('has_price') == 'no')
            {
                $productCollection = $productCollection->filter(function($product){
                    $hasPrice = FALSE;

                    if (!$product->variants->isEmpty())
                    {
                        $hasPrice = TRUE;
                        foreach ($product->variants as $key2=>$variant)
                        {
                            if ($variant->free_item == 'no')
                            {
                                if ( ($variant->normal_price == 0) && ($variant->price == 0) )
                                {
                                    $hasPrice = FALSE;
                                    break;
                                }
                            }
                        }
                    }

                    if (Input::get('has_price') == 'yes')
                        return $hasPrice;
                    elseif (Input::get('has_price') == 'no')
                        return !$hasPrice;
                });
            }
        }

        return $productCollection;
    }

    public function filterResultsByVariantMediaContent(Illuminate\Database\Eloquent\Collection $productCollection)
    {
        if (Input::has('has_variant_mediacontent'))
        {
            if (Input::get('has_variant_mediacontent') == 'yes' or Input::get('has_variant_mediacontent') == 'no')
            {
                $productCollection = $productCollection->filter(function($product) {

                    $hasMediaContent = FALSE;

                    if (!$product->variants->isEmpty())
                    {
                        $hasMediaContent = TRUE;

                        foreach ($product->variants as $key2=>$variant)
                        {
                            if ($variant->mediaContents->isEmpty())
                            {
                                $hasMediaContent = FALSE;
                                break;
                            }
                        }
                    }

                    if (Input::get('has_variant_mediacontent') == 'yes')
                        return $hasMediaContent;
                    elseif (Input::get('has_variant_mediacontent') == 'no')
                        return !$hasMediaContent;
                });
            }
        }

        return $productCollection;
    }

    public function filterResultsByVariantAllowsInstallment(Illuminate\Database\Eloquent\Collection $productCollection)
    {
        // get any product where product or its variant allow installment
        if (Input::get('product_allow_installment')==='yes' && Input::has('variant_allow_installment') && Input::get('variant_allow_installment') === 'yes')
        {
            $productCollection = $productCollection->filter(function($product) {

                // if product itself allow installment; return true immediately
                if ($product->allow_installment)
                    return true;

                // if any of its variant allow installment; return true
                if (!$product->variants->isEmpty())
                {
                    foreach ($product->variants as $variant)
                    {
                        if ($variant->allow_installment)
                        {
                            return true;
                        }
                    }
                }

                return false;
            });
        }
        // get any product where allow installment
        else if (Input::get('product_allow_installment')==='yes')
        {
            $productCollection = $productCollection->filter(function($product) {
                return $product->allow_installment;
            });
        }
        else if (Input::get('product_allow_installment')==='no')
        {
            $productCollection = $productCollection->filter(function($product) {
                return ! $product->allow_installment;
            });
        }

        return $productCollection;
    }



    /*
    public function findRevision($id)
    {
        return $this->product->findOrFail($id);
    }
    */

    /*
    public function search($criteria, $value)
    {
        if($criteria==='brand' && $value!=false)
        {
            $this->product = $this->product->ofBrand($value);
        }
        elseif($criteria==='vendor' && $value!=false)
        {
            $this->product = $this->product->sellsByVendor($value);
        }
        elseif(($criteria==='product_line' || $criteria==='product-line') && $value!=false)
        {
            $this->product = $this->product->hasProductLine($value);
        }
        elseif(($criteria==='title' || $criteria==='name') && $value!=false)
        {
            $this->product = $this->product->hasTitle($value);
        }

        return $this;
    }
    */

    public function saveDraft($id, $data)
    {
        $product = $this->find($id);

        foreach ($data as $key=>$value)
        {
            if (!empty($value))
            {
                $product->{$key} = $value;
            }
        }

        $productDirty = $product->getDirty();

        if ( empty($productDirty) )
        {
            return TRUE;
        }

        $user = Sentry::getUser();

        $revision = $product->revisions()->whereIn('status', array('draft', 'rejected', 'approved'))->where('editor_id', $user->id)->get();

        // if ( $product->revisions()->ofStatus(array('draft', 'rejected', 'approved'))->get()->isEmpty() )
        if ( $revision->isEmpty() )
        {
            // Create new Revision
            $revision = new Revision;

            $revision->value = json_encode($productDirty);
            $revision->status = 'draft';
            $revision->editor_id = $user->id;

            $product->revisions()->save($revision);
        }
        else
        {
            // $revision = $product->revisions()->ofStatus(array('draft', 'rejected', 'approved'))->first();
            $revision = $revision->first();

            // $revision->value = json_encode($productDirty);
            $revisionValue = json_decode($revision->value, TRUE);

            foreach ($productDirty as $key=>$val)
            {
                $revisionValue[$key] = $val;
            }

            $revision->value = json_encode($revisionValue);
            $revision->status = 'draft';
            $revision->editor_id = $user->id;

            $revision->save();
        }

        return TRUE;
    }

    public function savePublishProduct($productId, $revisionId)
    {
        $revision = Revision::find($revisionId);
        $product = Product::find($productId);

        $draftData = json_decode($revision->value, TRUE);

        foreach ($draftData as $key=>$val)
        {
            if ($key=='price')
            {
                continue;
            }

            $product->{$key} = $val;
        }

        if (empty($product->published_at) or $product->published_at == '0000-00-00 00:00:00')
        {
            $product->published_at = date('Y-m-d H:i:s');
        }

        // Save Price
        if (isset($draftData['price']))
        {
            foreach ($draftData['price'] as $k=>$v)
            {
                // $variant = ProductVariant::find($v['variant_id']);
                $variant = $product->variants()->where('id', $v['variant_id'])->first();
                $variant->price = $v['price'];
                $variant->normal_price = $v['normal_price'];
                $variant->free_item = $v['free_item'];
                $variant->save();

                $variant->load('specialDiscounts');
                foreach ($variant->specialDiscounts as $key => $specialDiscount) {
                    $specialDiscount->buildDiscountPrice($variant->netPrice);
                    $specialDiscount->save();
                }
            }
        }

        // Save Product.
        // If Product Has Missing Some Content Data, Don't Publish Immediately.
        if ( empty($product->title) or empty($product->brand_id) or empty($product->description) or empty($product->key_feature) or (!$product->isProductHasPrice()))
        {
            $product->status = 'draft';
        }
        else
        {
            $product->status = 'publish';
        }
        $product->save();

        $product->rebuildVariantsTitle();

        // ElasticUtils::updateProduct($product);

        if ($product->active == '1' && $product->status == 'publish')
        {
            ElasticUtils::updateProduct($product);
        }
        else
        {
            ElasticUtils::removeProduct($product);
        }

        // Published, Change Revision Status.
        $revision->status = 'publish';
        $revision->save();
    }

}