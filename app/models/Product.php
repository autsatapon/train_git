<?php

class Product extends PCMSModel {

	protected $fillable = array('name', 'description');

    protected $hidden = array('pkey', 'deleted_at');

    public static $rules = array(
        //'title'        => 'required|unique:products',
        'title'        => 'required',
        'product_line' => 'required',
    );

    public static $labels = array(
        'pkey' => 'Product Key',
    	'title' => 'Product Name',
    	'product_line' => 'Product Line',
    	'key_feature' => 'Key Feature',
    );

    public static function boot()
    {
        parent::boot();

        static::deleted(function($model)
        {
            ProductVariant::where('product_id', $model->id)->delete();
        });

        static::restored(function($model)
        {
            ProductVariant::withTrashed()->where('product_id', $model->id)->restore();
        });

        static::saved(function($model)
        {
            if (is_null($model->published_at) && $model->status == 'publish')
            {
                $model->published_at = date('Y-m-d H:i:s');
                $model->save();
            }
        });
    }

    public function revisions()
    {
        return $this->morphMany('Revision', 'revisionable');
    }

	public function brand()
	{
		return $this->belongsTo('Brand');
	}

    public function metadatas()
    {
        return $this->morphMany('MetaData', 'metadatable');
    }

    public function collections()
    {
        return $this->belongsToMany('Collection', 'product_collections', 'product_id', 'collection_id')->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany('ProductVariant');
    }

    public function styleTypes()
    {
        //return $this->belongsToMany('StyleType');
        return $this->belongsToMany('StyleType', 'product_style_type', 'product_id', 'style_type_id')->withPivot('media_set');
    }

    public function styleOptions()
    {
        return $this->softBelongsToMany('StyleOption', 'product_style_options', 'product_id', 'style_option_id')->withPivot('id', 'text', 'meta', 'sort_order')->withTimestamps();
    }

    public function productStyleOptions()
    {
        return $this->hasMany('ProductStyleOption');
    }

    public function getMediaStyleTypeAttribute()
    {
        foreach ($this->styleTypes as $styleType)
            if ($styleType->pivot->media_set == 1)
                return $styleType;
        return null;
    }

    public function getInstallmentAttribute($value)
    {
        return json_decode($value);
    }

    public function getAllowInstallmentAttribute()
    {
        if (isset($this->installment->allow))
            return $this->installment->allow;
        return false;
    }

    public function getInstallmentPeriodsAttribute()
    {
        if ($this->allow_installment)
            return $this->installment->periods;
        return array();
    }

    public function getDiscountEndedAttribute()
    {
        $discountEnded = '';

        $variants = $this->variants;

        if ( !$variants->isEmpty() )
        {
            foreach ($variants as $variant)
            {
                $ended = (string) $variant->activeSpecialDiscount()->pluck('ended_at');

                if ($discountEnded == '')
                {
                    $discountEnded = $ended;
                }
                else
                {
                    if ($discountEnded < $ended)
                    {
                        $discountEnded = $ended;
                    }
                }
            }
        }

        return $discountEnded;
    }

    public function methods()
    {
        return $this->belongsToMany('ShippingMethod', 'product_shipping_methods', 'product_id', 'shipping_method_id');
    }

    public function shippingMethods()
    {
        return $this->hasMany('ProductShippingMethod');
    }

    public function scopeHasTitle($query, $title)
    {
    	if ($title != false)
        {
			return $query->where('title','like','%'.$title.'%');
        }

		return $query;
    }

    public function scopeHasProductLine($query, $product_line)
    {
        if ($product_line != false)
        {
            return $query->where('product_line','like','%'.$product_line.'%');
        }

        return $query;
    }

    public function scopeAllowCod($query, $allow_cod = null)
    {
    	if ($allow_cod != false)
        {
	    	return $query->where('allow_cod', $allow_cod==='yes' ? 1 : 0);
        }

	    return $query;
    }

    public function scopeOfBrand($query, $brand_id)
    {
    	if ($brand_id != false)
        {
			return $query->where('brand_id', $brand_id);
        }

		return $query;
    }

    public function scopeHasStatus($query, $status)
    {
        return $query->whereStatus($status);
    }

    public function scopeSellsByVendor($query, $vendor)
    {
    	if ($vendor == false)
        {
    		return $query;
        }

    	$variants = ProductVariant::distinct('product_id')->where('vendor_id', $vendor)->lists('product_id');

        if(!empty($variants))
            return $query->whereIn('id', $variants);

        return $query->where('id', 0);
    }

    public function scopeHasTag($query, $tag)
    {
        if ($tag != false)
        {
            return $query->where('tag','like','%'.$tag.'%');
        }

        return $query;
    }

    public function scopeExcept($query, $except)
    {
    	if ($except == false)
        {
    		return $query;
        }

	    return $query->whereNotIn('id', $except);
    }

    public function scopeIsAllowsInstallment($query, $allowance)
    {
        if ($allowance == false)
        {
            return $query;
        }

        return $query->where('installment', 'like' , '%'.rtrim(json_encode(array('allow'=>($allowance==='yes'?true:false))),'}').'%');
    }

    /**
     * reletion to media_content
     */
    public function mediaContents()
    {
        return $this->morphMany('MediaContent', 'mediable');
    }

    public function getImageAttribute()
    {
        $mediaImage = $this->mediaContents->first();
        if ( !empty($mediaImage) )
        {
            return (string) UP::lookup($mediaImage->attachment_id)->scale('m');
        }
        return null;
    }

    public function getImagesAttribute()
    {
        $mediaImage = $this->mediaContents()->where('mode', 'image')->first();
        if ( !empty($mediaImage) )
        {
            return array(
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
        return array();
    }

    public function scopeIsContentExist($query, $hasProductContent)
    {
        if ($hasProductContent == 'yes')
        {
            return $query->where(function($q1){
                $q1->where(function($q2){
                    $q2->where('description', '!=', '')->whereNotNull('description');
                })->orWhere(function($q2){
                    $q2->where('key_feature', '!=', '')->whereNotNull('key_feature');
                });
            });
        }
        elseif ($hasProductContent == 'no')
        {
            return $query->where(function($q1){
                $q1->where(function($q2){
                    $q2->whereNull('description')->orWhere('description', '');
                })->where(function($q2){
                    $q2->whereNull('key_feature')->orWhere('key_feature', '');
                });
            });
        }

        return $query;
    }

/*
    public function scopeNoContentExist($query)
    {
        return $query->where(function($q1){
            $q1->where(function($q2){
                $q2->where('description', '')->orWhere('description', null);
            })->where(function($q2){
                $q2->where('key_feature', '')->orWhere('key_feature', null);
            });
        });
    }

    public function scopeContentExist($query)
    {
        return $query->where(function($q1){
            $q1->where(function($q2){
                $q2->where('description', '!=', '')->where('description', '!=', null);
            })->orWhere(function($q2){
                $q2->where('key_feature', '!=', '')->where('key_feature', '!=', null);
            });
        });
    }
*/

    public function scopeHasVariants($query) // renamed from scopeWithVariants
    {
        return $query->has('variants');
    }

    public function scopeVariantsHavePrice($query) // renamed from scopeVariantsHasPrice
    {
        return $query->with(array('variants' => function($q){
            // $q->where('price', 0);
        }));
    }

    public function scopeVariantsDontHavePrice($query) // renamed from scopeVariantsNotHasPrice
    {
        return $query->with(array('variants' => function($q){

        }));
    }

    public function isProductHasPrice()
    {
        if ($this->variants->isEmpty())
        {
            return FALSE;
        }

        foreach ($this->variants as $key=>$variant)
        {
            if ($variant->free_item == 'no')
            {
                if ( ($variant->normal_price == 0) && ($variant->price == 0) )
                {
                    return FALSE;
                }
            }
        }

        return TRUE;
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

    public function rebuildVariantsTitle()
    {
        if (empty($this->translates))
        {
            $this->load('translates');
        }

        if (empty($this->variants))
        {
            $this->load('variants.styleOptions');
        }

        $productStyleOptions = ProductStyleOption::with('translates')->whereProductId($this->getKey())->get();

        // get style option for create title of variants
        foreach ($this->variants as $variant)
        {

            // get style option text for merge as variant title
            $styleOptionText = array();

            foreach ($variant->styleOptions as $key => $variantStyleOption)
            {
                $filter = function($model) use ($variantStyleOption)
                {
                    return ($model->style_option_id == $variantStyleOption->id);
                };
                $productStyleOption = $productStyleOptions->filter($filter)->first();
                $styleOptionText[] = $productStyleOption ? $productStyleOption->text : $variantStyleOption->text;
            }
            // set it!
            $variant->title = $this->title.(count($styleOptionText) ? ' ('.implode(', ', $styleOptionText).')' : '');

            $variantTextTranslated = array();

            // loop each translate locales
            // we will translate each locale follow locale that had under product
            foreach ($this->translates as $key => $productTranslate) {

                // get locale from product
                $locale = $productTranslate->locale;

                // get all style option text
                $styleOptionText = array();
                foreach ($variant->styleOptions as $key => $variantStyleOption)
                {
                    $styleOptionTranslated = "";
                    $filter = function($model) use ($variantStyleOption)
                    {
                        return ($model->style_option_id == $variantStyleOption->id);
                    };
                    $productStyleOption = $productStyleOptions->filter($filter)->first();
                    if ($productStyleOption)
                    {
                        $filterLocale = function($model) use ($locale)
                        {
                            return ($model->locale == $locale);
                        };
                        $productStyleOptionTranslate = $productStyleOption->translates->filter($filterLocale)->first();
                        if ($productStyleOptionTranslate)
                        {
                            $styleOptionTranslated = $productStyleOptionTranslate->text;
                        }
                    }

                    $styleOptionText[] = $styleOptionTranslated ?: $variantStyleOption->text;
                }

                // try to get product title that translated
                $productTitle = $productTranslate->title ?: $this->title;

                $variantTextTranslated[$locale] = $productTitle.(count($styleOptionText) ? ' ('.implode(', ', $styleOptionText).')' : '');
            }

            $variant->setTranslate('title', $variantTextTranslated);
            $variant->save();
        }
    }

    public function rebuildStyleTypeMediaSet()
    {
        $this->load('styleTypes');

        if ($this->styleTypes->count() < 1)
        {
            return false;
        }

        $mediaSet = false;

        //try to find color
        $filterColor = function(StyleType $styleType)
        {
            return ($styleType->id == 1);
        };
        $styleTypeColor = $this->styleTypes->filter($filterColor)->first();

        // found style type color
        if ($styleTypeColor)
        {
            $pivot = $styleTypeColor->pivot;
            $pivot->media_set = 1;
            $pivot->save();

            $mediaSet = true;
        }

        // try to get another media set is 1 and not color
        $filterMediaSet1 = function(StyleType $styleType)
        {
            return ($styleType->pivot->media_set == 1 && $styleType->id != 1);
        };
        $styleTypeMediaSet1 = $this->styleTypes->filter($filterMediaSet1);

        // loop style type
        foreach ($styleTypeMediaSet1 as $key => $styleType)
        {
            // if media set is true so another is 0
            if ($mediaSet == true)
            {
                $pivot = $styleType->pivot;
                $pivot->media_set = 0;
                $pivot->save();
            }

            $mediaSet = true;
        }

        // if media set is false so don't have media set
        // find first and set it
        if ($mediaSet == false)
        {
            $pivot = $this->styleTypes->first()->pivot;
            $pivot->media_set = 1;
            $pivot->save();
            $mediaSet = true;
        }

        return $this->styleType;

    }

    // public function getSlugAttribute()
    // {
    //     return Str::slug($this->attributes['title']);
    // }

}

Product::observe(new Observer\ProductObserver);

// use ProductSearchObserver on every environments except local
// because Local does't have elastic search
if (App::environment() != "local" && App::environment() != "local-test")
{
    Product::observe(new Observer\ProductSearchObserver);
}
