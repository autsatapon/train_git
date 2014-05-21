<?php

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Collection as Collection;

class PKeysRepository {

    protected static $cacheName = "pkey_cache";

    protected static $sectionName = "pkey";

    protected $collection = null;

    protected $relationType;

    protected $relationsLoaded = false;

    protected $exclude = array();

    /**
     * construct
     * @param Collection $collection
     * @param string $type allow parent or child only
     */
    public function __construct(Collection $collection, $type)
    {
        $this->collection = $collection;

        if (! in_array($type, array("parent", "child")))
        {
            throw new Exception("Relation type can be 'parent' or 'child' only.");
        }
        else
        {
            $this->relationType = $type;
        }
    }

    /**
     * Prepare reposity
     * @param Collection $collection
     * @param string $type allow parent or child only
     * @return PKeysRepository
     */
    public static function prepare(Collection $collection, $type)
    {
        return new static($collection, $type);
    }

    /**
     * preapre single model
     * @param  Model $model
     * @param string $type allow parent or child only
     * @return PKeysRepository
     */
    public static function prepareSingle(Model $model, $type)
    {
        $newCollection = new Collection;
        $newCollection->add($model);

        return new static($newCollection, $type);
    }

    public function get()
    {
        $pkeyList = array();

        // get pkey list
        foreach ($this->collection as $key => $model) {
            $pkey = $this->storeGet($model);
            $pkeyList[$model->getKey()] = $pkey;
        }

        return $pkeyList;
    }

    public function setExclude($index, Array $pkey)
    {
        $this->exclude[$index] = $pkey;

        return $this;
    }

    public function getExclude($index)
    {
        if (empty($this->exclude[$index]))
        {
            return array();
        }

        return $this->exclude[$index];
    }

    public static function flush($className)
    {
        $model = new $className;
        self::storeFlush($model);
    }

    protected function loadRelations()
    {
        if ($this->relationsLoaded)
        {
            return true;
        }

        // get main logic
        $logic = $this->getLogic();

        /**
         * load relations logic
         */

        // get relation logic
        $relations = array_get($logic, 'relations');

        if (! $relations || ! is_array($relations))
        {
            throw new Exception("Can't get relations from logic. Please check method in repo.");
        }

        // load relations
        $this->collection->load($relations);

        $this->relationsLoaded = true;
    }

    /**
     * build pkey relation
     * @return array
     */
    protected function build(Model $model)
    {
        $this->loadRelations();

        $logic = $this->getLogic();

        /**
         * get fetcher logic
         */
        $fetcher = array_get($logic, 'fetcher');

        if (! $fetcher || ! is_callable($fetcher))
        {
            throw new Exception("Can't get fetcher from logic. Please check method in repo.");
        }

        return $fetcher($model);
    }

    protected function getLogicProductVariantParent()
    {
        return array(
            'relations' => array(
                'product' => function($query) {
                    return $query->select("products.id", "products.pkey", "products.brand_id");
                },
                'product.brand' => function($query) {
                    return $query->select("brands.id", "brands.pkey");
                },
                'product.collections' => function($query)
                {
                    return $query->select("collections.id", "collections.pkey");
                }
            ),
            'fetcher' => function(Model $model)
            {
                $output = array($model->pkey);

                if ($model->product)
                {
                    $output[] = $model->product->pkey;
                    if ($model->product->brand)
                    {
                        $output[] = $model->product->brand->pkey;
                    }

                    if ($model->product->collections && $model->product->collections->count())
                    {
                        $output = array_merge($output, $model->product->collections->lists('pkey'));
                    }
                }

                return $output;
            }
        );
    }

    protected function getLogicCollectionChild()
    {
        $that = $this;
        return array(
            'relations' => array(
                'products' => function($query) {
                    return $query->select('products.id', 'products.pkey', 'products.brand_id');
                },
                'products.variants' => function($query) {
                    return $query->select('variants.id', 'variants.pkey', 'variants.product_id');
                }
            ),
            'fetcher' => function(Model $model) use ($that)
            {
                $output = array($model->pkey);

                $model->products->each(function($product) use (&$output, $that) {

                    // product in exclude list so skip it
                    $excludeProduct = $that->getExclude('product');
                    if (in_array($product->pkey, $excludeProduct))
                    {
                        return false;
                    }

                    $output[] = $product->pkey;

                    $product->variants->each(function($variant) use (&$output, $that) {

                        // variant in exclude list so skip it
                        $excludeVariant = $that->getExclude('variant');
                        if (in_array($variant->pkey, $excludeVariant))
                        {
                            return false;
                        }

                        $output[] = $variant->pkey;
                    });
                });

                return $output;
            }
        );
    }

    protected function getLogicBrandChild()
    {
        $that = $this;
        return array(
            'relations' => array(
                'products' => function($query) {
                    return $query->select('products.id', 'products.pkey', 'products.brand_id');
                },
                'products.variants' => function($query) {
                    return $query->select('variants.id', 'variants.pkey', 'variants.product_id');
                }
            ),
            'fetcher' => function(Model $model) use ($that)
            {
                $output = array($model->pkey);

                $model->products->each(function($product) use (&$output, $that) {

                    // product in exclude list so skip it
                    $excludeProduct = $that->getExclude('product');
                    if (in_array($product->pkey, $excludeProduct))
                    {
                        return false;
                    }

                    $output[] = $product->pkey;

                    $product->variants->each(function($variant) use (&$output, $that) {

                        // variant in exclude list so skip it
                        $excludeVariant = $that->getExclude('variant');
                        if (in_array($variant->pkey, $excludeVariant))
                        {
                            return false;
                        }

                        $output[] = $variant->pkey;
                    });
                });

                return $output;
            }
        );
    }

    protected function getLogicProductChild()
    {
        $that = $this;
        return array(
            'relations' => array(
                'variants' => function($query) {
                    return $query->select('variants.id', 'variants.pkey', 'variants.product_id');
                }
            ),
            'fetcher' => function(Model $model) use ($that)
            {
                $output = array($model->pkey);

                $model->variants->each(function($variant) use (&$output, $that) {

                    // variant in exclude list so skip it
                    $excludeVariant = $that->getExclude('variant');
                    if (in_array($variant->pkey, $excludeVariant))
                    {
                        return false;
                    }

                    $output[] = $variant->pkey;
                });

                return $output;
            }
        );
    }

    /**
     * get logic from method
     * @return array
     */
    protected function getLogic()
    {
        $method = "getLogic".ucfirst(get_class($this->collection->first())).ucfirst($this->relationType);

        if (! method_exists($this, $method))
        {
            throw new Exception("Can't call {$method} method. Please create it with logic.");
        }

        return $this->$method();
    }

    /**
     * get data
     * @return mixed
     */
    protected function storeGet(Model $model)
    {
        // $data = Cache::tags(self::getSectionKey($model))->get($this->getCacheKey($model));

        // if (! $data)
        // {
        //     $data = $this->build($model);
        //     $this->storePut($model, $data);
        //     // d($data);
        // }

        $data = $this->build($model);

        return $data;
    }

    /**
     * flush cache
     * @param  Model  $model
     */
    protected static function storeFlush(Model $model)
    {
        // return Cache::tags(self::getSectionKey($model))->flush();
    }

    /**
     * clear cache
     */
    protected function storeForget(Model $model)
    {
        // return Cache::tags(self::getSectionKey($model))->forget($this->getCacheKey($model));
    }

    /**
     * store data to cache
     * @param  mixed $data
     */
    protected function storePut(Model $model, &$data)
    {
        // return Cache::tags(self::getSectionKey($model))->forever($this->getCacheKey($model), $data);
    }

    /**
     * get cache key
     * @return string
     */
    protected function getCacheKey(Model $model)
    {
        $class = get_class($model);
        return self::$cacheName."_{$class}_{$this->relationType}_{$model->getKey()}_".md5(json_encode($this->exclude));
    }

    protected static function getSectionKey(Model $model)
    {
        $class = get_class($model);
        return self::$sectionName."-{$class}";
    }
}
