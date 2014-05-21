<?php

class ProductSearchController extends AdminController {

    protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Product', URL::to('products'));
        $this->theme->breadcrumb()->add('Search Product', URL::to('products/search'));
    }


    public function getIndex()
    {

        $products = $this->product->getExecuteFormSearch();
        //$products->load('brand', 'variants', 'mediaContents');
        $products->load('brand', 'variants', 'revisions');


        //s(Input::all(), $products->toArray());

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);



        $this->theme->setTitle('Search Product');
        $this->data['products'] = $products;
        return $this->theme->of('products.search.index', $this->data)->render();
    }

    public function getExport()
    {
        $products = $this->product->getExecuteFormSearch();
        $products->load('brand', 'variants', 'revisions', 'mediaContents', 'variants.mediaContents', 'variants.variantStyleOption', 'variants.variantStyleOption.styleType');

        $productLink = array();
        $variantLink = array();
        $variantStyleOption = array();

        foreach ($products as $key => $product)
        {
            foreach ($product->mediaContents as $k2 => $media)
            {
                $productLink[$product->id][$media->mode][] = $media->link;
            }

            // $product->variants->load('mediaContents', 'variantStyleOption');
            foreach($product->variants as $k2 => $variant)
            {
                // $variant->variantStyleOption->load('styleType');

                foreach ($variant->mediaContents as $k3 => $media)
                {
                    $variantLink[$product->id][$variant->id][$media->mode][] = $media->link;
                }

                // Get Variant Style & Option
                foreach ($variant->variantStyleOption as $k4 => $styleOption)
                {
                    $variantStyleOption[$product->id][$variant->id][$styleOption->styleType->name] = $styleOption->text;
                }
            }
        }

        $this->data['variantStyleOption'] = $variantStyleOption;
        $this->data['productLink'] = $productLink;
        $this->data['variantLink'] = $variantLink;
        $this->data['products'] = $products;

        // d($productLink); die();

        /*
        foreach ($products as $product)
        {
            if ( !$product->variants->isEmpty() )
            {
                $product->variants->each(function($variant){
                    $variant->load('mediaContents');
                });
            }
        }
        */

        // $exportData = View::make('products.search.export')->with('products', $products)->render();

        return View::make('products.search.export', $this->data);
    }

    public function getPopup($type=null)
    {
        if (empty($view))
        {
            $view = array();
        }

        // define type
        $allowedType = array('variant', 'product', 'brand', 'collection', 'exclude-variant', 'exclude-product');

        if ( ! in_array($type, $allowedType))
        {
            $type = 'variant';
        }

        $products = array();

        if ($type == 'exclude-variant')
        {
            if ( ! Input::has('pkeys'))
            {
                return 'Excluding product required products selected';
//                throw new Exception('Excluding required pkeys');
            }

            $items = explode(',', Input::get('pkeys'));
            $products = Product::whereIn('pkey', $items)->get();
        }
        else if ($type == 'exclude-product')
        {
            if ( ! Input::has('pkeys'))
            {
                return 'Excluding product required brand selected';
//                throw new Exception('Excluding required pkeys');
            }

            $items = explode(',', Input::get('pkeys'));
            if (Input::get('parent') == 'collection')
            {
                $products = Product::whereHas('collections', function($query) use ($items) {
                    return $query->whereIn('pkey', $items);
                })->get();
            }
            else
            {
                $brands = Brand::whereIn('pkey', $items)->lists('id');
                $products = Product::whereIn('brand_id', $brands)->get();
            }

        }
        else if ($type == 'brand')
        {
            $brands = Brand::all()->lists('name', 'pkey');
            $view['brands'] = $brands;
        }
        else if ($type == 'collection')
        {
            $collection = Collection::query()->rootCollection();



            if (! Input::has('app_id'))
            {
                $collection->with(implode('.', array_fill(0, 20, 'children')));
            }
            else
            {
                $collectionRelations = array();
                for ($i=1; $i <= 20; $i++) {
                    $collectionRelations[implode('.', array_fill(0, $i, 'children'))] = function($query)
                    {
                        $query->whereHas('apps', function($query) { $query->whereId(Input::get('app_id')); });
                    };
                }
                $collection->with($collectionRelations);
                $collection->whereHas('apps', function($query) { $query->whereId(Input::get('app_id')); });
            }

            $collections = $collection->get();


// sd(DB::getQueryLog());
            $view['collections'] = $collections;
        }
        else if (
            Input::has('product') || Input::has('tag') || Input::has('brand') || Input::has('vendor_id') || Input::has('product_line') || Input::has('product_allow_cod') || Input::has('has_product_content'))
        {
            $products = $this->product->getExecuteFormSearch();
            $products->load('brand', 'variants', 'revisions');
        }

        $view['type'] = $type;
        $view['products'] = $products;

        $this->theme->layout('popup-iframe');

        // popup manage items plugin
//        $this->theme->asset()->container('footer')->usePath()->add('bootstrap-affix-js', 'bootstrap/js/bootstrap-affix.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('popup-iframe-js', 'admin/js/popup-iframe.js', 'jquery');
        $this->theme->asset()->usePath()->add('popup-iframe-css', 'admin/css/popup-iframe.css', 'pcms');

        return $this->theme->of('products.search.popup.'.$type, $view)->render();
    }

}