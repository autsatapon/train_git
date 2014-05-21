<?php
class Collection extends PCMSModel {

    public static $rules = array(
        'name' => 'required',
    );

    public static $labels = array(
        'name' => 'Collection Name',
        'slug' => 'Slug',
        'pkey' => 'Collection Key',
        'publish_for' => 'Publishes for',
    );

    public function apps()
    {
        return $this->belongsToMany('PApp', 'apps_collections', 'collection_id', 'app_id');
    }

    public function products()
    {
        return $this->belongsToMany('Product', 'product_collections', 'collection_id', 'product_id')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo('Collection', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('Collection', 'parent_id');
    }

    public function bestSeller()
    {
        return $this->hasOne('Collection', 'parent_id')->where('collection_type', 'best_seller');
    }

    public function metadatas()
    {
        return $this->morphMany('MetaData', 'metadatable');
    }

    /**
     * Brand has many files upload.
     * @return AttachmentRelate
     */
    public function files()
    {
        return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
    }

    // public function getSlugAttribute()
    // {
    //     return Str::slug($this->attributes['name']);
    // }

    public function getEssayAttribute()
    {
        $essay = '';
        $currentApp = PApp::getCurrentApp();
        if ( !empty($currentApp) )
        {
            $essay = $this->metadatas()->where('app_id', $currentApp->id)->where('key', 'essay')->pluck('value');
        }

        return (string) $essay;
    }

    public function getImageAttribute()
    {
        if (!empty($this->attributes['attachment_id']))
        {
            return (string) UP::lookup($this->attributes['attachment_id']);
        }

        return '';
/*
        $image = $this->files()->get();
        if ($image->isEmpty())
        {
            return '';
        }

        return (string) UP::lookup($image->first()->attachment_id);
*/
    }

    public function getThumbnailAttribute()
    {
        if (!empty($this->attributes['attachment_id']))
        {
            return (string) UP::lookup($this->attributes['attachment_id'])->scale('square');
        }

        return '';
/*
        $image = $this->files()->get();
        if ($image->isEmpty())
        {
            return '';
        }

        return (string) UP::lookup($image->first()->attachment_id)->scale('square');
*/
    }

    /* ตั้งชื่อฟังก์ชันผิดอะ .... เดี๋ยวต้องแก้ (จำได้ว่า น่าจะใช้อยู่แค่ใน collection อย่างเดียวนะ) */
    public function getAllNodeAttribute($value)
    {
        $arr = array();
        $arr[] = array(
            'id' => $this->id,
            'name' => $this->name
        );

        $iteration = $this->parent_id;
        while ($iteration > 0)
        {
            $parent = Collection::find($iteration);
            $arr[] = array(
                'id' => $parent->id,
                'name' => $parent->name
            );
            $iteration = $parent->parent_id;
        }

        return array_reverse($arr);
    }

    public function getParentsAttribute($value)
    {
        $arr = array();
        /*
        $arr[] = array(
            'pkey' => $this->pkey,
            'name' => $this->name,
            'is_category' => $this->is_category,
        );
        */

        $iteration = $this->parent_id;
        while ($iteration > 0)
        {
            $parent = Collection::find($iteration);
            $arr[] = array(
                'pkey' => $parent->pkey,
                'name' => $parent->name,
                /*'is_category' => $parent->is_category,*/
            );
            $iteration = $parent->parent_id;
        }

        return array_reverse($arr);
    }

    /* Query Scope */
    public function scopeIsCategory($query)
    {
        return $query->where('is_category', '=', 1);
    }

    /* Query Scope */
    public function scopeRootCollection($query)
    {
        return $query->where("{$this->table}.parent_id", '=', 0)->where('collection_type', '=', 'default');
    }

    /* Query Scope */
    public function scopeIncurrentapp($query)
    {
        $currentApp = PApp::getCurrentApp();
        $currentAppId = $currentApp->id;

        $collectionIds = DB::table('apps_collections')->where('app_id', $currentAppId)->lists('collection_id');

        if ( ! $collectionIds)
        {
            $collectionIds = array(-99999);
        }

        // if ( !empty($collectionIds))
        // {
            return $query->whereIn('id', $collectionIds);
        //}

        //return $query->where('name', '=', 'asgghashahwatahasgfsaf');
    }


    public function getProductExBestSeller($id)
    {
        $products = DB::table('product_collections')->where('collection_id', $id)->get();
        d($products);
    }

    public function getMetasAttribute()
    {
        if ($this->metadatas->isEmpty())
        {
            return array();
        }

        $rawArr = $this->metadatas->toArray();

        $metas = array();
        foreach ($rawArr as $meta)
        {
            $metas[$meta['key']] = $meta['value'];
        }

        return $metas;
    }

}

Collection::observe(new Observer\CollectionObserver);