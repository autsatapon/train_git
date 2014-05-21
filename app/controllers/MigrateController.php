<?php

ini_set('memory_limit','1024M');
set_time_limit(0);
ini_set('display_errors', 1);

class MigrateController extends Controller {

    public function __construct()
    {
        // DB::setDefaultConnection('pcms_migrate');
    }

    public function getFixParentCategory()
    {
        $itmParentIds = DB::table('migrated_category')->where('parent_id', 0)->lists('category_id');
        $pcmsParentIds = DB::table('category_maps')->whereIn('itruemart_id', $itmParentIds)->lists('pcms_id');

        $garbage = Collection::where('parent_id', 0)->whereNotIn('id', $pcmsParentIds)->get();
        $ids = Collection::where('parent_id', 0)->whereNotIn('id', $pcmsParentIds)->lists('id');
        // d($ids);

        while ( !empty($ids) )
        {
            // soft Delete $garbage ทั้งหมด
            foreach ($garbage as $key => $val)
            {
                $val->delete();
            }

            $garbage = Collection::whereIn('parent_id', $ids)->get();
            $ids = Collection::whereIn('parent_id', $ids)->lists('id');

        }

        echo 'complete';
    }

    public function getCategory()
    {
        $rawCategories = MigratedCategory::where('migrate_status', 'no')->get();

        $errorCount = 0;

        foreach ($rawCategories as $key => $val)
        {
            try
            {
                // Create Collection
                $collection = new Collection;
                $collection->parent_id = 0;
                $collection->is_category = 1;
                $collection->name = $val->name_thai;
                $collection->slug = Str::slug(str_replace('&amp;', '', $val->name_eng));
                $collection->save();

                // Mapping
                DB::table('category_maps')->insert(
                    array(
                        'itruemart_id' => $val->category_id,
                        'pcms_id'      => $collection->id,
                        'pkey'         => $collection->pkey
                    )
                );

                // insert App Collections
                DB::table('apps_collections')->insert(
                    array('collection_id' => $collection->id, 'app_id' => 1)
                );

                // Create Translate
                DB::table('translates')->insert(
                    array(
                        'locale'           => 'en_US',
                        'languagable_id'   => $collection->id,
                        'languagable_type' => 'Collection',
                        'name'             => $val->name_eng,
                    )
                );

                // Metadata
                if ( !empty($val->images) )
                {
                    $banner = new MetaData;
                    $banner->app_id = 1;
                    $banner->type = 'file';
                    $banner->key = 'banner';
                    $banner->value = $val->images;
                    $banner->metadatable_id = $collection->id;
                    $banner->metadatable_type = 'Collection';
                    $banner->save();
                }
            }
            catch (Exception $e)
            {
                $errorLog = array(
                    'type'          => 'Category',
                    'record_id'     => $val->category_id,
                    'error_message' => 'Create Category : ' . $e->getMessage(),
                    'created_at'    => date('Y-m-d H:i:s')
                );

                DB::table('migrate_error_logs')->insert($errorLog);

                d($errorLog);
                $errorCount++;
            }

            // Update Flag
            $val->migrate_status = 'yes';
            $val->save();
        }

        // ReCheck Parent Category
        $this->checkParentCategory();

        $countAllCategories = count($rawCategories);
        $completeCount = $countAllCategories - $errorCount;

        d("{$countAllCategories} Categories, {$errorCount} errors, {$completeCount} complete.");
    }

    private function checkParentCategory()
    {
        $collections = Collection::all();

        foreach ($collections as $collection)
        {
            // Recheck Parent ID
            $itmCategoryId = DB::table('category_maps')->where('pcms_id', $collection->id)->pluck('itruemart_id');
            $itmParentId = DB::table('migrated_category')->where('category_id', $itmCategoryId)->pluck('parent_id');

            if ($itmParentId == 0)
            {
                $pcmsParentId = 0;
            }
            else
            {
                $pcmsParentId = DB::table('category_maps')->where('itruemart_id', $itmParentId)->pluck('pcms_id');
            }

            if ($pcmsParentId == NULL)
            {
                continue;
            }

            $collection->parent_id = $pcmsParentId;
            $collection->save();
        }
    }

    public function getUpdateCategory()
    {
        $rawCategories = MigratedCategory::where('migrate_status', 'update')->get();

        $errorCount = 0;

        foreach ($rawCategories as $key => $val)
        {
            try
            {
                // Find Collection
                $collectionId = DB::table('category_maps')->where('itruemart_id', $val->category_id)->pluck('pcms_id');
                $collection = Collection::find($collectionId);

                // Update
                $collection->name = $val->name_thai;
                $collection->slug = Str::slug(str_replace('&amp;', '', $val->name_eng));
                $collection->save();

                // Update Translate
                DB::table('translates')->where('languagable_id', $collection->id)->where('languagable_type', 'Collection')->update(
                    array(
                        'name'             => $val->name_eng,
                    )
                );

                // Metadata
                if ( !empty($val->images) )
                {
                    $banner = $collection->metadatas()->where('key', 'banner')->first();

                    if ( empty($banner) )
                    {
                        $banner = new MetaData;
                        $banner->app_id = 1;
                        $banner->type = 'file';
                        $banner->key = 'banner';
                        $banner->value = $val->images;
                        $banner->metadatable_id = $collection->id;
                        $banner->metadatable_type = 'Collection';
                        $banner->save();
                    }
                    else
                    {
                        $banner->value = $val->images;
                        $banner->save();
                    }
                }
            }
            catch (Exception $e)
            {
                $errorLog = array(
                    'type'          => 'Category',
                    'record_id'     => $val->category_id,
                    'error_message' => 'Update Category : ' . $e->getMessage(),
                    'created_at'    => date('Y-m-d H:i:s')
                );

                DB::table('migrate_error_logs')->insert($errorLog);

                d($errorLog);
                $errorCount++;
            }

            // Update Flag
            $val->migrate_status = 'yes';
            $val->save();
        }

        // ReCheck Parent Category
        $this->checkParentCategory();

        $countAllCategories = count($rawCategories);
        $completeCount = $countAllCategories - $errorCount;

        d("Update {$countAllCategories} Categories, {$errorCount} errors, {$completeCount} complete.");
    }

    public function getBrand()
    {
        $rawBrands =  MigratedBrand::where('migrate_status', 'no')->get();

        $errorCount = 0;

        foreach ($rawBrands as $key => $val)
        {
            try
            {
                // Create new Brand.
                $brand              = new Brand;
                $brand->name        = $val->name_thai;
                $brand->slug        = Str::slug(str_replace('&amp;', '', $val->name_eng));
                $brand->description = $val->history_thai;
                $brand->save();

                // Create Translate
                DB::table('translates')->insert(
                    array(
                        'locale'           => 'en_US',
                        'languagable_id'   => $brand->id,
                        'languagable_type' => 'Brand',
                        'name'             => $val->name_eng,
                        'description'      => $val->history_eng
                    )
                );

                // Mapping
                DB::table('brand_maps')->insert(
                    array(
                        'itruemart_id' => $val->brand_id,
                        'pcms_id'      => $brand->id,
                        'pkey'         => $brand->pkey
                    )
                );

                // Insert MetaData (Video)
                if ( !empty($val->vdo) )
                {
                    $video = new MetaData;
                    $video->app_id = 1;
                    $video->type = 'link';
                    $video->key = 'video';
                    $video->value = $val->vdo;
                    $video->metadatable_id = $brand->id;
                    $video->metadatable_type = 'Brand';
                    $video->save();
                }

                // Insert MetaData (Brand Banner)
                if ( !empty($val->logo_banner) )
                {
                    $bannerLogo = new MetaData;
                    $bannerLogo->app_id = 1;
                    $bannerLogo->type = 'file';
                    $bannerLogo->key = 'banner-logo';
                    $bannerLogo->value = $val->logo_banner;
                    $bannerLogo->metadatable_id = $brand->id;
                    $bannerLogo->metadatable_type = 'Brand';
                    $bannerLogo->save();
                }

                // Insert MetaData (Brand Logo Icon)
                if (!empty($val->logo_icon))
                {
                    $bannerIcon = new MetaData;
                    $bannerIcon->app_id = 1;
                    $bannerIcon->type = 'file';
                    $bannerIcon->key = 'banner-icon';
                    $bannerIcon->value = $val->logo_icon;
                    $bannerIcon->metadatable_id = $brand->id;
                    $bannerIcon->metadatable_type = 'Brand';
                    $bannerIcon->save();
                }

                // Insert MetaData (Brand Logo Flashsale)
                if (!empty($val->logo_flashsale))
                {
                    $bannerFlashsale = new MetaData;
                    $bannerFlashsale->app_id = 1;
                    $bannerFlashsale->type = 'file';
                    $bannerFlashsale->key = 'banner-flashsale';
                    $bannerFlashsale->value = $val->logo_flashsale;
                    $bannerFlashsale->metadatable_id = $brand->id;
                    $bannerFlashsale->metadatable_type = 'Brand';
                    $bannerFlashsale->save();

                    // upload รูป Logo ด้วย package UP
                    $imageUrl = $val->logo_flashsale;

                    $results = UP::inject(array('remote' => true))->upload($brand, $imageUrl)->getResults();
                    $attachmentId = $results['original']['fileName'];

                    $brand->attachment_id = $attachmentId;
                    $brand->save();
                }

                // Complete.
                echo "<p>Brand ID {$val->brand_id} - Complete.</p>";
            }
            catch (Exception $e)
            {
                $errorLog = array(
                    'type'          => 'Brand',
                    'record_id'     => $val->brand_id,
                    'error_message' => 'Create Brand : ' . $e->getMessage(),
                    'created_at'    => date('Y-m-d H:i:s')
                );

                DB::table('migrate_error_logs')->insert($errorLog);

                d($errorLog);
                $errorCount++;
            }

            // Update Flag
            $val->migrate_status = 'yes';
            $val->save();
        }

        $countAllBrands = count($rawBrands);
        $completeCount = $countAllBrands - $errorCount;

        d("{$countAllBrands} Brands, {$errorCount} errors, {$completeCount} complete.");
    }

    public function getUpdateBrand()
    {
        $rawBrands =  MigratedBrand::where('migrate_status', 'update')->get();

        $errorCount = 0;

        foreach ($rawBrands as $key => $val)
        {
            try
            {
                // Find Brand
                $brandId = DB::table('brand_maps')->where('itruemart_id', $val->brand_id)->pluck('pcms_id');
                $brand = Brand::find($brandId);

                // Update Brand Data
                $brand->name        = $val->name_thai;
                $brand->slug        = Str::slug(str_replace('&amp;', '', $val->name_eng));
                $brand->description = $val->history_thai;
                $brand->save();

                // Update Translate
                DB::table('translates')->where('languagable_id', $brand->id)->where('languagable_type', 'Brand')->update(
                    array(
                        'name'             => $val->name_eng,
                        'description'      => $val->history_eng
                    )
                );

                // Insert/Update MetaData (Video)
                if ( !empty($val->vdo) )
                {
                    $video = $brand->metadatas()->where('key', 'video')->first();

                    if ( empty($video) )
                    {
                        // Create new if not exist
                        $video = new MetaData;
                        $video->app_id = 1;
                        $video->type = 'link';
                        $video->key = 'video';
                        $video->value = $val->vdo;
                        $video->metadatable_id = $brand->id;
                        $video->metadatable_type = 'Brand';
                        $video->save();
                    }
                    else
                    {
                        // Update
                        $video->value = $val->vdo;
                        $video->save();
                    }
                }

                // Insert/Update MetaData (Brand Banner)
                if ( !empty($val->logo_banner) )
                {
                    $bannerLogo = $brand->metadatas()->where('key', 'banner-logo')->first();

                    if ( empty($bannerLogo) )
                    {
                        $bannerLogo = new MetaData;
                        $bannerLogo->app_id = 1;
                        $bannerLogo->type = 'file';
                        $bannerLogo->key = 'banner-logo';
                        $bannerLogo->value = $val->logo_banner;
                        $bannerLogo->metadatable_id = $brand->id;
                        $bannerLogo->metadatable_type = 'Brand';
                        $bannerLogo->save();
                    }
                    else
                    {
                        // Update
                        $bannerLogo->value = $val->logo_banner;
                        $bannerLogo->save();
                    }
                }

                // Insert/Update MetaData (Brand Logo Icon)
                if (!empty($val->logo_icon))
                {
                    $bannerIcon = $brand->metadatas()->where('key', 'banner-icon')->first();

                    if ( empty($bannerIcon) )
                    {
                        $bannerIcon = new MetaData;
                        $bannerIcon->app_id = 1;
                        $bannerIcon->type = 'file';
                        $bannerIcon->key = 'banner-icon';
                        $bannerIcon->value = $val->logo_icon;
                        $bannerIcon->metadatable_id = $brand->id;
                        $bannerIcon->metadatable_type = 'Brand';
                        $bannerIcon->save();
                    }
                    else
                    {
                        // Update
                        $bannerIcon->value = $val->logo_icon;
                        $bannerIcon->save();
                    }
                }

                // Insert MetaData (Brand Logo Flashsale)
                if (!empty($val->logo_flashsale))
                {
                    $bannerFlashsale = $brand->metadatas()->where('key', 'banner-flashsale')->first();

                    if ( empty($bannerIcon) )
                    {
                        $bannerFlashsale = new MetaData;
                        $bannerFlashsale->app_id = 1;
                        $bannerFlashsale->type = 'file';
                        $bannerFlashsale->key = 'banner-flashsale';
                        $bannerFlashsale->value = $val->logo_flashsale;
                        $bannerFlashsale->metadatable_id = $brand->id;
                        $bannerFlashsale->metadatable_type = 'Brand';
                        $bannerFlashsale->save();

                        // upload รูป Logo ด้วย package UP
                        $imageUrl = $val->logo_flashsale;

                        $results = UP::inject(array('remote' => true))->upload($brand, $imageUrl)->getResults();
                        $attachmentId = $results['original']['fileName'];

                        $brand->attachment_id = $attachmentId;
                        $brand->save();
                    }
                    else
                    {
                        // Update
                        if ($bannerFlashsale->value != $val->logo_flashsale)
                        {
                            $bannerFlashsale->value = $val->logo_flashsale;
                            $bannerFlashsale->save();

                            // upload รูป Logo ด้วย package UP
                            if ( !empty($brand->attachment_id) )
                            {
                                UP::remove($brand->attachment_id);
                            }

                            $imageUrl = $val->logo_flashsale;

                            $results = UP::inject(array('remote' => true))->upload($brand, $imageUrl)->getResults();
                            $attachmentId = $results['original']['fileName'];

                            $brand->attachment_id = $attachmentId;
                            $brand->save();
                        }
                    }
                }

                // Complete.
                echo "<p>Update Brand ID {$val->brand_id} - Complete.</p>";
            }
            catch (Exception $e)
            {
                $errorLog = array(
                    'type'          => 'Brand',
                    'record_id'     => $val->brand_id,
                    'error_message' => 'Update Brand : ' . $e->getMessage(),
                    'created_at'    => date('Y-m-d H:i:s')
                );

                DB::table('migrate_error_logs')->insert($errorLog);

                d($errorLog);
                $errorCount++;
            }

            // Update Flag
            $val->migrate_status = 'yes';
            $val->save();
        }

        $countAllBrands = count($rawBrands);
        $completeCount = $countAllBrands - $errorCount;

        d("Update {$countAllBrands} Brands, {$errorCount} errors, {$completeCount} complete.");
    }

    public function getImportExcel($type = '')
    {
        if ($type == 'brand')
        {
            $this->importBrandExcel();
        }
        elseif ($type == 'category')
        {
            $this->importCategoryExcel();
        }
        elseif ($type == 'product')
        {
            $this->importProductExcel();
        }
    }

    private function getObjectPhpExcel($path)
    {
        $objPHPExcel = PHPExcel_IOFactory::load($path);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        unset($sheetData[1]);

        return $sheetData;
    }

    private function importBrandExcel()
    {
        //path file.
        $pathFile = "./20140515-excel-migrate-itruemart/brand.xlsx";
        $sheetData = $this->getObjectPhpExcel($pathFile);

        foreach ($sheetData as $k => $v)
        {
            $rawData = array(
                'brand_id'       => (int) $v['A'],
                'name_thai'      => htmlspecialchars($v['B'],ENT_QUOTES, 'UTF-8'),
                'history_thai'   => htmlspecialchars($v['C'],ENT_QUOTES, 'UTF-8'),
                'slug_thai'      => $v['D'],
                'name_eng'       => htmlspecialchars($v['E'],ENT_QUOTES, 'UTF-8'),
                'history_eng'    => htmlspecialchars($v['F'],ENT_QUOTES, 'UTF-8'),
                'slug_eng'       => htmlspecialchars($v['G'],ENT_QUOTES, 'UTF-8'),
                'vdo'            => $v['H'],
                'logo_banner'    => $v['I'],
                'logo_icon'      => $v['J'],
                'logo_flashsale' => $v['K'],
                'migrate_status' => 'no'
            );

            $migratedBrand = MigratedBrand::where('brand_id', $rawData['brand_id'])->first();

            if ( empty($migratedBrand) )
            {
                $migratedBrand = MigratedBrand::insert($rawData);
            }
            else
            {
                foreach ($rawData as $key => $val)
                {
                    $migratedBrand->{$key} = $val;
                }

                $dirty = $migratedBrand->getDirty();

                if ( !empty($dirty) )
                {
                    d($dirty);
                    $migratedBrand->migrate_status = 'update';
                    $migratedBrand->save();
                }
            }
        }

        echo "Import Brand Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

    private function importCategoryExcel()
    {
        //path file.
        $pathFile = "./20140515-excel-migrate-itruemart/category.xlsx";
        $sheetData = $this->getObjectPhpExcel($pathFile);

        foreach ($sheetData as $k => $v)
        {
            $rawData = array(
                'category_id'    => (int) $v['A'],
                'parent_id'      => (int) $v['B'],
                'name_thai'      => $v['C'],
                'slug_thai'      => $v['D'],
                'name_eng'       => htmlspecialchars($v['E'],ENT_QUOTES, 'UTF-8'),
                'slug_eng'       => htmlspecialchars($v['F'],ENT_QUOTES, 'UTF-8'),
                'images'         => $v['G'],
                'migrate_status' => 'no'
            );

            $migratedCategory = MigratedCategory::where('category_id', $rawData['category_id'])->first();

            if ( empty($migratedCategory) )
            {
                $migratedCategory = MigratedCategory::insert($rawData);
            }
            else
            {
                foreach ($rawData as $key => $val)
                {
                    $migratedCategory->{$key} = $val;
                }

                $dirty = $migratedCategory->getDirty();

                if ( !empty($dirty) )
                {
                    d($dirty);
                    $migratedCategory->migrate_status = 'update';
                    $migratedCategory->save();
                }
            }
        }

        echo "Import Category Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

    private function importProductExcel()
    {
        $i = Input::get('file', 1);
        // for ($i=1; $i<=12; $i++)
        // {
            // if ($i == 2)
            // {
            //     continue;
            // }
            //path file.
            $pathFile = "./20140515-excel-migrate-itruemart/products_page_$i.xlsx";

            try
            {
                $objPHPExcel = PHPExcel_IOFactory::load($pathFile);
            }
            catch (\Exception $e)
            {
                throw new \Exception('Error read file:'.$pathFile. ' - '.$e->getMessage());
            }

            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            unset($sheetData[1]);
            $index = 0;
            $total_index_parent = ceil(count($sheetData)/100);

            $index_parent = 0;

            foreach ($sheetData as $k => $v)
            {
                if($index%$total_index_parent == 0)
                {
                    $index_parent++;
                }

                $product[$index_parent][$index] = array(
                    'sku_code'              =>$v['B'] ,
                    'product_id'            =>$v['C'] ,
                    'inventory_id'          =>$v['D'] ,
                    'material_code'         =>$v['E'] ,
                    'shop_id'               =>$v['F'] ,
                    'brand_name'            =>htmlspecialchars($v['G'],ENT_QUOTES, 'UTF-8'),
                    'barcode'               =>htmlspecialchars($v['H'],ENT_QUOTES, 'UTF-8'),
                    'title'                 =>htmlspecialchars($v['I'],ENT_QUOTES, 'UTF-8'),
                    'color'                 =>htmlspecialchars($v['J'],ENT_QUOTES, 'UTF-8'),
                    'size'                  =>$v['K'] ,
                    'normal_price'          =>$v['L'] ,
                    'special_price'         =>$v['M'] ,
                    'margin'                =>$v['N'] ,
                    'option'                =>$v['O'] ,
                    'stock'                 =>$v['P'] ,
                    'vendor_code'           =>$v['Q'] ,
                    'vendor_type'           =>$v['R'] ,
                    'vendor_stock'          =>$v['S'] ,
                    'product_status'        =>$v['T'] ,
                    'create_date'           =>$v['U'] ,
                    'title_eng'             =>htmlspecialchars($v['V'],ENT_QUOTES, 'UTF-8'),
                    'key_feture_thai'       =>htmlspecialchars($v['W'],ENT_QUOTES, 'UTF-8'),
                    'key_feture_eng'        =>htmlspecialchars($v['X'],ENT_QUOTES, 'UTF-8'),
                    'description_thai'      =>htmlspecialchars($v['Y'],ENT_QUOTES, 'UTF-8'),
                    'description_eng'       =>htmlspecialchars($v['Z'],ENT_QUOTES, 'UTF-8'),
                    'color_code'            =>$v['AA'] ,
                    'color_image'           =>$v['AB'] ,
                    'size_code'             =>$v['AC'] ,
                    'size_image'            =>$v['AD'] ,
                    'texture'               =>$v['AE'] ,
                    'texture_code'          =>$v['AF'] ,
                    'texture_image'         =>$v['AG'] ,
                    'product_image_original'=>$v['AH'] ,
                    'product_image_big'     =>$v['AI'] ,
                    'product_image_medium'  =>$v['AJ'] ,
                    'product_image_thumb'   =>$v['AK'] ,
                    'installment'           =>$v['AL'] ,
                    'installment_period'    =>$v['AM'] ,
                    'tags'                  =>htmlspecialchars($v['AN'],ENT_QUOTES, 'UTF-8'),
                    'suggestions'           =>htmlspecialchars($v['AO'],ENT_QUOTES, 'UTF-8'),
                    'brand_id'              =>$v['AP'] ,
                    'category_id'           =>$v['AQ'] ,
                    'category_name_thai'    =>htmlspecialchars($v['AR'],ENT_QUOTES, 'UTF-8'),
                    'category_name_eng'     =>htmlspecialchars($v['AS'],ENT_QUOTES, 'UTF-8'),
                    'status_product'        => 'no'
                );
                $index++;
            }

            foreach($product as $key => $value)
            {
                MigratedProduct::insert($value);
            }

            // echo '<br> Migrated Product success<br>';

            // $nextFile = $i + 1;
            // echo "<a href=\"http://pcms-true.igetapp.com/migrate-itruemart/product-excel?cmd=run&file={$nextFile}\">next page</a>";
            // echo "<a href=\"http://pcms.alpha.itruemart.com/migrate-itruemart/product-excel?cmd=run&file={$nextFile}\">next page</a>";

            echo "Import Product Data from Excel - Complete";
            echo "<br> Path file - {$pathFile}";
        // }
    }

}