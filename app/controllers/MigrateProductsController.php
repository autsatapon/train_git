<?php

class MigrateProductsController extends BaseController {

    public function getUpdateElastic($id)
    {
        $product = Product::find($id);

        ElasticUtils::updateProduct($product);
    }

    public function __construct()
    {
        // DB::setDefaultConnection('pcms_migrate');
    }

    public function getIndex()
    {
        echo 'hello world';
    }

    private function updateElastic($id)
    {
        $product = Product::find($id);

        ElasticUtils::updateProduct($product);
    }

    public function getProducts()
    {
        echo '<p>';
        echo "Start Date Time - ";
        echo date('Y-m-d H:i:s');
        echo '</p>';

        $itmProductId = DB::table('migrated_product')->where('status_product', 'no')->orderBy('id', 'ASC')->pluck('product_id');
        if ( !$itmProductId )
        {
            echo 'ALL Product Complete!';
            return;
        }

        $rawVariants = DB::table('migrated_product')->where('product_id', $itmProductId)->get();

        try {
            d($itmProductId);

            // Create new Product.
            $product = $this->createProduct($rawVariants);

            // Product ID Mapping.
            $this->mappingProductId($itmProductId, $product);

            // Create Translate.
            $this->createTranslate($rawVariants, $product);

            // Insert product in Collection.
            $this->insertCollections($rawVariants, $product);

            // Create Variants.
            $this->createVariants($rawVariants, $product);

            // Insert Product Style Type.
            $this->createStyleTypeOptions($rawVariants, $product);

            // Insert Product Image and Product Options Image.
            $this->createProductsMediaContents($rawVariants, $product);

            // Update in Elastic Search
            ElasticUtils::updateProduct($product);

            // Update
            DB::table('migrated_product')->where('product_id', $itmProductId)->update(array('status_product' => 'yes'));

            echo "complete. <a href=\"".URL::current()."\">Fetch new Product.</a>";

            echo '<p>';
            echo "Complete Date Time - ";
            echo date('Y-m-d H:i:s');
            echo '</p>';

        } catch (Exception $e) {

            $errorLog = array(
                'type'          => 'Product',
                'record_id'     => $itmProductId,
                'error_message' => 'Create Product ($itmProductId = ' . $itmProductId . ') : ' . $e->getMessage(),
                'created_at'    => date('Y-m-d H:i:s')
            );

            DB::table('migrate_error_logs')->insert($errorLog);

            // Update
            DB::table('migrated_product')->where('product_id', $itmProductId)->update(array('status_product' => 'error'));

            d($e->getMessage());

            die();
        }

        echo '<script>';
        echo 'var delay = 5000;';
        echo 'setTimeout(function(){';
        echo 'window.location.reload();';
        echo '}, delay);';
        echo '</script>';
    }

    private function createProduct($rawVariants)
    {
        $firstRow = $rawVariants[0];
        $pcmsBrandId = DB::table('brand_maps')->where('itruemart_id', $firstRow->brand_id)->pluck('pcms_id');

        if (!$pcmsBrandId)
        {
            $brand = new Brand;
            $brand->name = $firstRow->brand_name;
            $brand->slug = Str::slug($brand->name);
            $brand->save();

            DB::table('brand_maps')->insert(
                array(
                    'itruemart_id' => $firstRow->brand_id,
                    'pcms_id'      => $brand->id,
                    'pkey'         => $brand->pkey,
                )
            );

            $pcmsBrandId = $brand->id;
        }

        $product = new Product;
        $product->title = $firstRow->title;
        $product->slug = Str::slug($firstRow->title_eng);
        $product->brand_id = $pcmsBrandId;
        $product->product_line = $firstRow->title;
        $product->description = htmlspecialchars_decode($firstRow->description_thai);
        $product->key_feature = htmlspecialchars_decode($firstRow->key_feture_thai);
        $product->tag = str_replace('|', ',', $firstRow->tags);
        $product->has_variants = (count($rawVariants) > 1) ? 1 : 0 ;
        $product->status = ($firstRow->product_status == 'Approved') ? 'publish' : 'incomplete' ;
        if($firstRow->installment == 'N')
        {
            $product->installment = json_encode(array('allow' => false));
        }
        else
        {
            $product->installment = json_encode(array('allow' => true, 'periods' => array($firstRow->installment_period)));
        }

        $product->published_at = $firstRow->create_date;
        $product->active = 1;

        $product->save();

        d('Create Product - Complete.');

        return $product;
    }

    private function mappingProductId($itmProductId, Product $product)
    {
        DB::table('product_maps')->insert(
            array(
                'itruemart_id' => $itmProductId,
                'pcms_id'      => $product->id,
                'pkey'         => $product->pkey
            )
        );

        d('Mapping Product ID - Complete.');
    }

    private function createTranslate($rawVariants, Product $product)
    {
        $firstRow = $rawVariants[0];

        DB::table('translates')->insert(
            array(
                'locale'           => 'en_US',
                'languagable_id'   => $product->id,
                'languagable_type' => 'Product',
                'title'            => $firstRow->title_eng,
                'description'      => htmlspecialchars_decode($firstRow->description_eng),
                'key_feature'      => htmlspecialchars_decode($firstRow->key_feture_eng)
            )
        );

        d('Create Product Translate - Complete.');
    }

    private function insertCollections($rawVariants, Product $product)
    {
        $firstRow = $rawVariants[0];

        $itmCategoryId = $firstRow->category_id;

        // $collectionIds = array();

        $pcmsCollectionId = DB::table('category_maps')->where('itruemart_id', $itmCategoryId)->pluck('pcms_id');

        $currentId = $pcmsCollectionId;
        do
        {
            $collection = Collection::find($currentId);

            // Bug ??? - Itruemart มีส่ง Category ที่ไม่ได้ migrate เข้ามาด้วย ...
            // Create ใหม่ให้มันไปก่อนนะ
            if (!$collection)
            {
                // ไม่ Create แล้วนะ ... Break แม่งไปเลย
                break;

                $collection = new Collection;
                $collection->parent_id = 0;
                $collection->name = $firstRow->category_name_eng;
                $collection->slug = Str::slug($collection->name);
                $collection->save();

                DB::table('category_maps')->insert(
                    array(
                        'itruemart_id' => $firstRow->category_id,
                        'pcms_id'      => $collection->id,
                        'pkey'         => $collection->pkey,
                    )
                );

                DB::table('apps_collections')->insert(
                    array(
                        'collection_id' => $collection->id,
                        'app_id'        => 1
                    )
                );
            }

            DB::table('product_collections')->insert(
                array(
                    'product_id'    => $product->id,
                    'collection_id' => $collection->id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                )
            );

            $currentId = $collection->parent_id;
        }
        while($currentId != 0);

        d('Insert Product into Collections - Complete.');
    }

    private function createVariants($rawVariants, Product $product)
    {
        foreach ($rawVariants as $row)
        {
            $variant = new ProductVariant;
            $variant->inventory_id = $row->inventory_id;
            $variant->product_id = $product->id;
            $variant->material_code = $row->material_code;
            // material_code
            // vendor
            // allow_installment
            // installment_period
            $variant->title = $row->title;
            if ( !empty($row->color) )
            {
                $variant->title .= ' ' . $row->color;
            }
            if ( !empty($row->size) )
            {
                $variant->title .= ' ' . $row->size;
            }
            $variant->shop_id = $row->shop_id;

            switch ($row->vendor_type)
            {
                case '3.1' :
                    $variant->stock_type = 3;
                    break;
                case '3.2' :
                    $variant->stock_type = 5;
                    // $variant->stock_type = 4;
                    break;
                case '4.1' :
                    $variant->stock_type = 4;
                    // $variant->stock_type = 5;
                    break;
                case '4.2' :
                    $variant->stock_type = 6;
                    break;
                default :
                    // $row->vendor_type == 1 or $row->vendor_type == 2
                    $variant->stock_type = $row->vendor_type;
                    break;
            }

            if (intval($row->special_price) > 0)
            {
                $variant->normal_price = $row->normal_price;
                $variant->price = $row->special_price;
            }
            else
            {
                $variant->normal_price = 0;
                $variant->price = $row->normal_price;
            }

            // $variant->vendor_id = ''; // null ?
            // $variant->master_id = ''; // where ? vendor_code ??
            $variant->vendor_id = NULL; // null ?
            $variant->master_id = NULL; // where ? vendor_code ??

            $variant->save();

            DB::table('variant_maps')->insert(
                array(
                    'itruemart_id' => $row->id,
                    'pcms_id'      => $variant->id,
                    'pkey'         => $variant->pkey
                )
            );
        }

        d('Create Variants - Complete.');
    }

    private function createStyleTypeOptions($rawVariants, Product $product)
    {
        // at table style_type, do nothing.

        // at table product_style_type, add relation between this product and style_type.
        $firstRow = $rawVariants[0];

        if ( !empty($firstRow->color) )
        {
            $mediaSet = ( !empty($firstRow->color_image) ) ? 1 : 0 ;

            DB::table('product_style_type')->insert(array(
                'product_id'    => $product->id,
                'style_type_id' => 1,
                'media_set'     => $mediaSet
            ));
        }

        if ( !empty($firstRow->size) )
        {
            // Media Set เป็น 0 ชัวร์ๆ (เพราะตามข้อมูลดิบ ไม่มีรูปผูกกับ Size)
            DB::table('product_style_type')->insert(array(
                'product_id'    => $product->id,
                'style_type_id' => 2,
                'media_set'     => 0
            ));
        }


        foreach ($rawVariants as $row)
        {
            $variantId = DB::table('variant_maps')->where('itruemart_id', $row->id)->pluck('pcms_id');

            if ( !empty ($row->color) )
            {
                if ( !empty($row->color_image) )
                {
                    $rowColorMeta = json_encode(array('type' => 'image', 'value' => $row->color_image));
                }
                else
                {
                    $rowColorMeta = json_encode(array('type' => 'text', 'value' => $row->color));
                }

                // at table style_option
                $colorStyleOption = StyleOption::where('text', $row->color)->where('style_type_id', 1)->first();
                if ( empty($colorStyleOption) )
                {
                    $colorStyleOption = new StyleOption;
                    $colorStyleOption->style_type_id = 1;
                    $colorStyleOption->text = $row->color;
                    $colorStyleOption->meta = $rowColorMeta;
                    $colorStyleOption->save();
                }

                // at table product_style_option
                $productStyleOptionRow = DB::table('product_style_options')->where('product_id', $product->id)->where('style_option_id', $colorStyleOption->id)->first();
                if ( empty($productStyleOptionRow) )
                {
                    // Get Insert ID
                    $insertId = '';

                    // Insert Product Style Option , and get ID
                    $insertId = DB::table('product_style_options')->insertGetId(array(
                        'product_id'      => $product->id,
                        'style_option_id' => $colorStyleOption->id,
                        'text'            => $colorStyleOption->text,
                        'meta'            => $rowColorMeta
                    ));

                    // Insert Product Style Option Media Contents
                    $this->createOptionsMediaContents($row, $insertId);
                }

                // at table variant_style_option
                DB::table('variant_style_options')->insert(array(
                    'variant_id'      => $variantId,
                    'style_type_id'   => 1,
                    'style_option_id' => $colorStyleOption->id,
                ));
            }

            if ( !empty ($row->size) )
            {
                $rowSizeMeta = json_encode(array('type' => 'text', 'value' => $row->size));

                // at table style_option
                $sizeStyleOption = StyleOption::where('text', $row->size)->where('style_type_id', 2)->first();

                if ( empty($sizeStyleOption) )
                {
                    $sizeStyleOption = new StyleOption;
                    $sizeStyleOption->style_type_id = 2;
                    $sizeStyleOption->text = $row->size;
                    $sizeStyleOption->meta = $rowSizeMeta;
                    $sizeStyleOption->save();
                }

                // at table product_style_option
                $productStyleOptionRow = DB::table('product_style_options')->where('product_id', $product->id)->where('style_option_id', $sizeStyleOption->id)->first();
                if ( empty($productStyleOptionRow) )
                {
                    DB::table('product_style_options')->insert(array(
                        'product_id'      => $product->id,
                        'style_option_id' => $sizeStyleOption->id,
                        'text'            => $sizeStyleOption->text,
                        'meta'            => $rowSizeMeta
                    ));
                }

                // at table variant_style_option
                DB::table('variant_style_options')->insert(array(
                    'variant_id'      => $variantId,
                    'style_type_id'   => 2,
                    'style_option_id' => $sizeStyleOption->id,
                ));
            }
        }

        d('Create Style Type and Style Options. - Complete.');
    }

    private function createMediaContents($mediable_type, $mediable_id, $imagesArr)
    {
        // $mediable_type = 'Product' or $mediable_type = 'ProductStyleOption'
        if ($mediable_type == 'Product')
        {
            $model = Product::find($mediable_id);
        }
        elseif ($mediable_type == 'ProductStyleOption')
        {
            $model = ProductStyleOption::find($mediable_id);
        }

        // Do Something
        foreach ($imagesArr as $imageUrl)
        {
            if ( empty($imageUrl) )
            {
                continue;
            }

            // Create new Media Content.
            $mediaContent = new MediaContent;
            $mediaContent->mode = 'image';
            $mediaContent->mediable_type = $mediable_type;
            $mediaContent->mediable_id = $mediable_id;
            $mediaContent->attachment_id = '';
            $mediaContent->sort_order = 99;
            $mediaContent->author_id = 1;
            $mediaContent->save();

            // Upload
            try
            {
                d("Upload From {$imageUrl}");
                $results = UP::inject(array('remote' => true))->upload($mediaContent, $imageUrl)->resize()->getResults();
                // d($results);
                if ( !empty($results['original']) && !empty( $results['original']['fileName'] ) )
                {
                    $attachmentId = $results['original']['fileName'];

                    $mediaContent->attachment_id = $attachmentId;
                    $mediaContent->save();
                }
            } catch (Exception $e) {

            }
        }

        d("Create Media Contents for {$mediable_type} - Complete.");
    }

    private function createProductsMediaContents($rawVariants, Product $product)
    {
        $imagesArr = $this->getProductMediaContentsImageUrls($rawVariants);
        return $this->createMediaContents('Product', $product->id, $imagesArr);
    }

    private function createOptionsMediaContents($row, $id)
    {
        $imagesArr = $this->getOptionsMediaContentsImageUrls($row);
        return $this->createMediaContents('ProductStyleOption', $id, $imagesArr);
    }

    private function getOptionsMediaContentsImageUrls($row)
    {
        $originalImages = explode('|', $row->product_image_original);

        foreach ($originalImages as $key=>$image)
        {
            $originalImages[$key] = str_replace(array(':Y', ':N'), '', $image);
        }

        return $originalImages;
    }

    private function getProductMediaContentsImageUrls($rawVariants)
    {
        if (count($rawVariants) == 1)
        {
            // ถ้า count($rawVariants == 1)
            // = ไม่มี variant
            // = Media Content ทุกรูปเป็นรูป product หมด (ไม่ผูกกับ product style option)
            $row = $rawVariants[0];
            $originalImages = explode('|', $row->product_image_original);

            foreach ($originalImages as $key=>$image)
            {
                $originalImages[$key] = str_replace(array(':Y', ':N'), '', $image);
            }
        }
        else
        {
            $originalImages = array();

            foreach ($rawVariants as $row)
            {
                $arr = explode('|', $row->product_image_original);

                // array filter , only :Y suffix
                $arr = array_values(array_filter($arr, function($var){
                    return preg_match('!:Y$!', $var);
                }));

                if (!empty($arr))
                {
                    foreach ($arr as $key=>$image)
                    {
                        $arr[$key] = str_replace(array(':Y', ':N'), '', $image);
                    }

                    foreach ($arr as $key=>$image)
                    {
                        if ( !in_array($image, $originalImages) )
                        {
                            $originalImages[] = $image;
                        }
                    }
                }
            }
        }

        return $originalImages;
    }






    public function getFixBrands()
    {
        $brands = Brand::all();

        foreach ($brands as $brand)
        {
            // เพิ่ม pkey ลง table map
            // DB::table('brand_maps')->where('pcms_id', $brand->id)->update( array('pkey' => $brand->pkey) );

            // Update Slug.
            // $brand->slug = Str::slug($brand->name);
            // $brand->save();

            // upload รูป Logo ด้วย package UP
            // $imageUrl = '';
            // $metaDatas = $brand->metadatas()->where('key', 'banner-flashsale')->first();

            // if ( !empty($metaDatas) )
            // {
            //     $imageUrl = $metaDatas->value;
            // }

            // if ( !empty($imageUrl) )
            // {
            //     $results = UP::inject(array('remote' => true))->upload($brand, $imageUrl)->getResults();
            //     $attachmentId = $results['original']['fileName'];

            //     $brand->attachment_id = $attachmentId;
            //     $brand->save();
            // }

            // echo "<p>{$brand->id} - Complete.</p>";
        }


    }

    public function getFixCollections()
    {
        $collections = Collection::all();

        // d(count($collections)); die();

        foreach ($collections as $collection)
        {
            // // Recheck Parent ID
            // $itmCategoryId = DB::table('category_maps')->where('pcms_id', $collection->id)->pluck('itruemart_id');
            // $itmParentId = DB::table('migrated_category')->where('category_id', $itmCategoryId)->pluck('parent_id');

            // if ($itmParentId == 0)
            // {
            //     // $queries = DB::getQueryLog();
            //     // $last_query = end($queries);
            //     // d($last_query);

            //     $pcmsParentId = 0;
            // }
            // else
            // {
            //     $pcmsParentId = DB::table('category_maps')->where('itruemart_id', $itmParentId)->pluck('pcms_id');
            // }

            // if ($pcmsParentId == NULL)
            // {
            //     $queries = DB::getQueryLog();
            //     $last_query = end($queries);
            //     d($last_query);
            // }

            // $collection->parent_id = $pcmsParentId;
            // // d($collection->getDirty());
            // $rs = $collection->save();









            // เพิ่ม pkey ลง table map
            // DB::table('category_maps')->where('pcms_id', $collection->id)->update( array('pkey' => $collection->pkey) );

            // เพิ่มข้อมูลลง app_collections
            // DB::table('apps_collections')->insert(
            //     array('collection_id' => $collection->id, 'app_id' => 1)
            // );

            // Update Slug.
            // $collectionNameEng = '';
            // $translate = $collection->translate();
            // if ( !empty($translate) )
            // {
            //     $collectionNameEng = $translate->name;
            // }
            // $collection->slug = Str::slug($collectionNameEng);
            // $collection->save();
        }
    }


    /* ทำทีหลังสุดไปเลยก็ได้ */
    public function getFixPkey()
    {
        // Delete ทุกอันที่ vid = 3, vid = 6

        // insert ใหม่
    }












    // public function getUpdateProducts()
    // {
    //     echo '<p>';
    //     echo "Start Date Time - ";
    //     echo date('Y-m-d H:i:s');
    //     echo '</p>';

    //     $itmProductId = DB::table('migrated_product')->where('status_product', 'update')->orderBy('id', 'ASC')->pluck('product_id');
    //     if ( !$itmProductId )
    //     {
    //         echo 'ALL Product Update Complete!';
    //         return;
    //     }

    //     $rawVariants = DB::table('migrated_product')->where('product_id', $itmProductId)->get();

    //     try {

    //         d($itmProductId);

    //         $pcmsId = DB::table('product_maps')->where('itruemart_id', $itmProductId)->pluck('pcms_id');

    //         if ( !empty($pcmsId) )
    //         {
    //             $product = Product::find($pcmsId);
    //         }
    //         else
    //         {
    //             // Create new Product & Mapping ID
    //             $product = $this->createProduct($rawVariants);
    //             $this->mappingProductId($itmProductId, $product);

    //             // Insert product in Collection.
    //             $this->insertCollections($rawVariants, $product);

    //             // Create Variants.
    //             $this->createVariants($rawVariants, $product);

    //             // Insert Product Style Type.
    //             $this->createStyleTypeOptions($rawVariants, $product);

    //             // Insert Product Image and Product Options Image.
    //             $this->createProductsMediaContents($rawVariants, $product);

    //             // Update in Elastic Search
    //             ElasticUtils::updateProduct($product);

    //             // Update
    //             DB::table('migrated_product')->where('product_id', $itmProductId)->update(array('status_product' => 'yes'));

    //             echo "complete. <a href=\"".URL::current()."\">Fetch new Product.</a>";

    //             echo '<p>';
    //             echo "Complete Date Time - ";
    //             echo date('Y-m-d H:i:s');
    //             echo '</p>';
    //         }

    //         $translate = DB::table('translates')->where('languagable_id', $product->id)->where('languagable_type', 'Product')->first();

    //         if ( !empty($translate) )
    //         {
    //             // Update Translate
    //             DB::table('translates')->where('languagable_id', $product->id)->where('languagable_type', 'Product')->update(
    //                 array(
    //                     'locale'           => 'en_US',
    //                     'languagable_id'   => $product->id,
    //                     'languagable_type' => 'Product',
    //                     'title'            => $firstRow->title_eng,
    //                     'description'      => htmlspecialchars_decode($firstRow->description_eng),
    //                     'key_feature'      => htmlspecialchars_decode($firstRow->key_feture_eng)
    //                 )
    //             );
    //         }
    //         else
    //         {
    //             // Create new Translate.
    //             $this->createTranslate($rawVariants, $product);
    //         }




    //     } catch (Exception $e) {
    //         $errorLog = array(
    //             'type'          => 'Product',
    //             'record_id'     => $itmProductId,
    //             'error_message' => 'Update Product ($itmProductId = ' . $itmProductId . ') : ' . $e->getMessage(),
    //             'created_at'    => date('Y-m-d H:i:s')
    //         );

    //         DB::table('migrate_error_logs')->insert($errorLog);

    //         // Update
    //         DB::table('migrated_product')->where('product_id', $itmProductId)->update(array('status_product' => 'error'));

    //         d($e->getMessage());

    //         // die();
    //     }

    //     echo '<script>';
    //     echo 'var delay = 5000;';
    //     echo 'setTimeout(function(){';
    //     echo 'window.location.reload();';
    //     echo '}, delay);';
    //     echo '</script>';
    // }







}