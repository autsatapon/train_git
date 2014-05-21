<?php

class DataMigrationController extends BaseController {

    protected $page, $skip, $take;

    public function __construct()
    {
        DB::setDefaultConnection('pcms_migrate');

        $this->page = (int) Input::get('page');
        $this->take = 10;

        if ($this->page < 1)
        {
            $this->page = 1;
        }

        $this->skip = $this->take * ($this->page - 1);

        $skip = (int) Input::get('skip');
        $take = (int) Input::get('take');

        if ($skip && $take)
        {
            $this->skip = $skip;
            $this->take = $take;
        }
    }

    public function getBrand()
    {
        // DB Connection
        $itruemartDB = DB::connection('itruemart');
        $pcmsDB = DB::connection('pcms_migrate');

        // Get term_id (brand) from iTrueMart Database
        $termIdArr = $itruemartDB->table('terms_hierarchies')->distinct()->where('vocabulary_id', 2)->orderBy('term_id', 'ASC')->skip($this->skip)->take($this->take)->lists('term_id');

        if (empty($termIdArr))
        {
            return;
        }

        // Get Brand Image
        $termImagesArr = $itruemartDB->table('terms_image')->select('term_id', 'img_path')->whereIn('term_id', $termIdArr)->whereWidth(170)->whereHeight(170)->lists('img_path', 'term_id');

        // Get Brand Banner
        $termBannersArr = $itruemartDB->table('terms_image')->select('term_id', 'img_path')->whereIn('term_id', $termIdArr)->whereHeight(290)->lists('img_path', 'term_id');

        // Get Brand Video
        $termVideosArr = $itruemartDB->table('terms')->select('term_id', 'embed_code')->whereIn('term_id', $termIdArr)->where('embed_code', '!=', '')->lists('embed_code', 'term_id');

        // Fill path for Brand Image and Banner
        $fillImgPath = function($img) {
            return 'http://cdn.itruemart.com/' . $img;
        };
        $termImagesArr = array_map($fillImgPath, $termImagesArr);
        $termBannersArr = array_map($fillImgPath, $termBannersArr);

        // Get term Data from iTrueMart Database
        $terms = $itruemartDB->table('terms_lang')->select('term_id', 'lang_id', 'name', 'description')->where('lang_id', 1)->whereIn('term_id', $termIdArr)->orderBy('term_id', 'ASC')->get();

        $mapDataArr = array();

        foreach ($terms as $term)
        {
            // Insert Brand Data
            $brand = new Brand;
            $brand->name = $term->name;
            $brand->description = $term->description;
            $saveStatus = $brand->save();

            if (!$saveStatus)
            {
                d($term, $brand->errors());
                continue;
            }

            // log to brand_migrated_logs
            $pcmsDB->table('brand_migrated_logs')->insert(array(
                'itm_term_id'   => $term->term_id,
                'pcms_brand_id' => $brand->id
            ));

            // Insert Brand Image
            if (!empty($termImagesArr[$term->term_id]))
            {
                $imagePath = $termImagesArr[$term->term_id];
                $attachment = UP::inject(array('remote' => true))->upload($brand, $imagePath)->getMasterResult();

                if ($attachment['isImage'])
                {
                    $brand->attachment_id = $attachment['fileName'];
                    $brand->save();
                }
                else
                {
                    UP::remove($attachment['fileName']);
                }
            }

            // ========== Insert Brand Meta Data ==========
            $itm_appId = 1;

            // Meta Data - Brand History
            MetaData::create(array(
                'app_id'       => $itm_appId,
                'meta_key'     => 'brand_history',
                'meta_value'   => $term->description,
                'metable_id'   => $brand->id,
                'metable_type' => 'Brand'
            ));

            // Meta Data - Brand Video
            if (!empty($termVideosArr[$term->term_id]))
            {
                MetaData::create(array(
                    'app_id'       => $itm_appId,
                    'meta_key'     => 'brand_video',
                    'meta_value'   => $termVideosArr[$term->term_id],
                    'metable_id'   => $brand->id,
                    'metable_type' => 'Brand'
                ));
            }

            // Meta Data - Brand Banner
            if (!empty($termBannersArr[$term->term_id]))
            {
                MetaData::create(array(
                    'app_id'       => $itm_appId,
                    'meta_key'     => 'brand_banner',
                    'meta_value'   => $termBannersArr[$term->term_id],
                    'metable_id'   => $brand->id,
                    'metable_type' => 'Brand'
                ));
            }

            // Map Data array (used when insert translate) (itm_term_id and pcms_brand_id)
            $mapDataArr[$term->term_id] = $brand->id;
        }

        // Get Brand Translate Data from iTrueMart Database (lang_id = 3)
        // (lang_id = 1 ==> EN, lang_id = 3 ==> TH)
        $termsTH = $itruemartDB->table('terms_lang')->select('term_id', 'lang_id', 'name', 'description')->where('lang_id', 3)->whereIn('term_id', $termIdArr)->orderBy('term_id', 'ASC')->get();

        // Insert Translate Data
        foreach ($termsTH as $term)
        {
            if (!isset($mapDataArr[$term->term_id]))
            {
                continue;
            }

            $pcmsDB->table('translates')->insert(array(
                'locale'           => 'th_TH',
                'languagable_id'   => $mapDataArr[$term->term_id],
                'languagable_type' => 'Brand',
                'name'             => $term->name,
                'description'      => $term->description
            ));
        }

        $nextPage = $this->page + 1;
        return "<p>Migrate Brand Data - Complete.</p><p><a href=\"/migrate/brand?dev=1&page={$nextPage}\">Next</a></p>";
    }

    public function getFixed()
    {
        $itruemartDB = DB::connection('itruemart');
        $pcmsDB = DB::connection('pcms_migrate');

        $termIdArr = array(772,779,782,787,790);
        $termsTH = $itruemartDB->table('terms_lang')->select('term_id', 'lang_id', 'name', 'description')->where('lang_id', 3)->whereIn('term_id', $termIdArr)->orderBy('term_id', 'ASC')->get();

        $i = 331;
        foreach ($termsTH as $term)
        {
            $pcmsDB->table('translates')->insert(array(
                'locale'           => 'th_TH',
                'languagable_id'   => $i,
                'languagable_type' => 'Collection',
                'name'             => $term->name,
            ));

            $i++;
        }
    }

    public function getCollection()
    {
        // DB Connection
        $itruemartDB = DB::connection('itruemart');
        $pcmsDB = DB::connection('pcms_migrate');

        // Get term_id (Category) from iTrueMart Database
        $termHierarchies = $itruemartDB->table('terms_hierarchies')->distinct()->where('vocabulary_id', 1)->orderBy('term_id', 'ASC')->skip($this->skip)->take($this->take);

        $termIdParentArr = $termHierarchies->lists('term_parent_id', 'term_id');
        $termIdArr = array_keys($termIdParentArr);

        if (empty($termIdArr))
        {
            return;
        }

        // Get Collection Image
        $termImagesArr = $itruemartDB->table('terms_image')->select('term_id', 'img_path')->whereIn('term_id', $termIdArr)->lists('img_path', 'term_id');

        // Fill path for Brand Image and Banner
        $fillImgPath = function($img) {
            return 'http://cdn.itruemart.com/' . $img;
        };
        $termImagesArr = array_map($fillImgPath, $termImagesArr);

        // Get term Data from iTrueMart Database
        $termDatas = $itruemartDB->table('terms_lang')->select('term_id', 'lang_id', 'name', 'description')->where('lang_id', 1)->whereIn('term_id', $termIdArr)->orderBy('term_id', 'ASC')->get();

        $mapDataArr = array();
        foreach ($termDatas as $term)
        {
            $collection = new Collection;
            $collection->parent_id = 0;
            $collection->name = $term->name;
            $collection->slug = Str::slug($collection->name);
            $collection->is_category = 1;

            // Insert Data into migrated_collections
            $saveStatus = $collection->save();

            if (!$saveStatus)
            {
                d($term, $collection->errors());
                continue;
            }

            // Map Data array (between itm_term_id and pcms_collection_id)
            $mapDataArr[$term->term_id] = $collection->id;

            // Insert Log to collection_migrated_logs
            $pcmsDB->table('collection_migrated_logs')->insert(array(
                'itm_term_id'   => $term->term_id,
                'pcms_collection_id' => $collection->id
            ));

            // Set parent_id
            if ($termIdParentArr[$term->term_id] > 0)
            {
                $collectionParentId = $pcmsDB->table('collection_migrated_logs')
                                                ->where('itm_term_id', $termIdParentArr[$term->term_id])
                                                ->pluck('pcms_collection_id');
                if ($collectionParentId)
                {
                    $collection->parent_id = $collectionParentId;
                    $collection->save();
                }
            }

            // Insert Data into migrated_apps_collections (itruemart app, app_id = 1)
            $pcmsDB->table('apps_collections')->insert(array(
                'app_id'   => 1,
                'collection_id' => $collection->id
            ));

            // Insert Collection Image.
            if (!empty($termImagesArr[$term->term_id]))
            {
                $attachment = UP::inject(array('remote' => true))->upload($collection, $termImagesArr[$term->term_id])->getMasterResult();
                if ($attachment['isImage'])
                {
                    $collection->attachment_id = $attachment['fileName'];
                    $collection->save();
                }
                else
                {
                    UP::remove($attachment['fileName']);
                }
            }
        }

        // Insert Translate data
        $termsTH = $itruemartDB->table('terms_lang')->select('term_id', 'lang_id', 'name', 'description')->where('lang_id', 3)->whereIn('term_id', $termIdArr)->orderBy('term_id', 'ASC')->get();

        foreach ($termsTH as $term)
        {
            if (!isset($mapDataArr[$term->term_id]))
            {
                continue;
            }

            $pcmsDB->table('translates')->insert(array(
                'locale'           => 'th_TH',
                'languagable_id'   => $mapDataArr[$term->term_id],
                'languagable_type' => 'Collection',
                'name'             => $term->name
            ));
        }

        $nextPage = $this->page + 1;
        return "<p>Migrate Collection Data - Complete.</p><p><a href=\"/migrate/collection?dev=1&page={$nextPage}\">Next</a></p>";
    }





/*
    public function getRemove()
    {
        $pcmsDB = DB::connection('pcms_migrate');

        $attachments = $pcmsDB->table('attachment_relates')->lists('attachment_id');

        foreach ($attachments as $val)
        {
            UP::remove($val);
        }

        d($attachments); die();
    }
*/
}