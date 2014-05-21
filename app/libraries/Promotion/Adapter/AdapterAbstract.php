<?php namespace Promotion\Adapter;

abstract class AdapterAbstract {

    protected $route = null;

    protected $ids = array();

    protected $excludeProductIds = array();

    protected $excludeVariantIds = array();

    protected $includeProductIds = array();

    protected $includeVariantIds = array();

    public function setRoute($route, $ids = array())
    {
        $this->route = ucfirst($route);

        if (is_string($ids))
        {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->ids = $ids;

        return $this;
    }

    public function setExcludeProducts($ids)
    {
        if (is_string($ids))
        {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->excludeProductIds = $ids;

        return $this;
    }

    public function setExcludeVariants($ids)
    {
        if (is_string($ids))
        {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->excludeVariantIds = $ids;

        return $this;
    }

    public function setIncludeProducts($ids)
    {
        if (is_string($ids))
        {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->includeProductIds = $ids;

        return $this;
    }

    public function setIncludeVariants($ids)
    {
        if (is_string($ids))
        {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->includeVariantIds = $ids;

        return $this;
    }

    public function findVariantIds()
    {
        $variantIds = array();

        switch ($this->route)
        {
            case 'Brand' :

                $variantIds = $this->findProductsByBrand()->findVariantsByProduct()->findVariants();

                break;

            case 'Product' :

                $variantIds = $this->findVariantsByProduct()->findVariants();

                break;

            case 'Variant' :

                $variantIds = $this->findVariants();

                break;
        }

        return $variantIds;
    }

    protected function findProductsByBrand() //$includeIds = array(), $excludeIds = array())
    {
        $productIds = array();

        $brands = \Brand::with('products')->whereIn('id', $this->ids)->get();

        foreach ($brands as $brand)
        {
            if ($brand->products->isEmpty()) continue;

            foreach ($brand->products as $product)
            {
                $productIds[] = $product->id;
            }
        }

        $this->productIds = $productIds;

        return $this;
    }

    protected function findVariantsByProduct() //$includeIds = array(), $excludeIds = array())
    {
        if ($this->route == 'Brand' and empty($this->productIds))
        {
            return $this;
        }

        if (empty($this->productIds))
        {
            $this->productIds = $this->ids;
        }

        $this->productIds = array_diff($this->productIds, $this->excludeProductIds);

        $this->productIds = array_merge($this->productIds, $this->includeProductIds);

        // $products = \Product::with('variants')->whereIn('id', $this->productIds)->get();
        $products = \Product::with('variants')->whereIn('id', $this->productIds)->whereStatus('publish')->get();

        $variantIds = array();

        foreach ($products as $product)
        {
            foreach ($product->variants as $variant)
            {
                $variantIds[] = $variant->id;
            }
        }

        $this->variantIds = $variantIds;

        return $this;
    }

    protected function findVariants() //$includeIds = array(), $excludeIds = array())
    {
        $this->variantIds = array_diff($this->variantIds, $this->excludeVariantIds);

        $this->variantIds = array_merge($this->variantIds, $this->includeVariantIds);

        return $this->variantIds;

    }

}