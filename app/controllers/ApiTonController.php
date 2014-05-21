<?php

class ApiTonController extends ApiBaseController {

    protected $page, $skip, $take;

    protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->page = (int) Input::get('page');
        if ($this->page < 1)
        {
            $this->page = 1;
        }

        $this->take = (int) Input::get('item_per_page');
        if ($this->take < 1)
        {
            $this->take = 10;
        }

        $this->skip = $this->take * ($this->page - 1);
    }

    public function product($app, $pkey)
    {
        $productArr = $this->product->getProductByPkey($pkey);

        if (!$productArr)
        {
            return API::createResponse(FALSE, 404);
        }

        return API::createResponse($productArr);
    }

    public function listCollectionProducts($app, $collectionKey)
    {
        $responseData = array(
            'products'   => array(),
            'page'       => '',
            'total_page' => '',
            'item'       => '',
            'total_item' => ''
        );

        $collection = Collection::where('pkey', $collectionKey)->first();

        // If Empty $collection, Return 404 Response.
        if (empty($collection))
        {
            return API::createResponse(FALSE, 404);
        }

        // $loadArr = array('brand', 'variants', 'variants.mediaContents' => function($q){ return $q->onlyMode('image'); });
        $loadArr = array('brand', 'variants', 'mediaContents' => function($q){ return $q->orderBy('sort_order', 'asc')->onlyMode('image'); });
        $products = $collection->products()->with($loadArr)->where('status', 'publish')->skip($this->skip)->take($this->take)->get();

        // If Empty $products->isEmpty(), Return 404 Response.
        if ($products->isEmpty())
        {
            return API::createResponse(FALSE, 404);
        }

        $visibleProductFields = array('pkey', 'title', 'brand', 'image_cover', 'price_range', 'net_price_range', 'special_price_range');
        $visibleBrandFields = array('pkey', 'name');

        foreach ($products as $product)
        {
            $product->image_cover = '';
            $product->price_range = '';

            $product->setVisible($visibleProductFields);
            $product->brand->setVisible($visibleBrandFields);

            $mediaImage = $product->mediaContents->first();
            if ( !empty($mediaImage) )
            {
                $product->image_cover = array(
                    'normal' => $mediaImage->link,
                    'thumbnails' => array(
                        'small'     => (string) UP::lookup($mediaImage->attachment_id)->scale('s'),
                        'medium'    => (string) UP::lookup($mediaImage->attachment_id)->scale('m'),
                        'square'    => (string) UP::lookup($mediaImage->attachment_id)->scale('square'),
                        'large'     => (string) UP::lookup($mediaImage->attachment_id)->scale('l'),
                    )
                );
            }

            $priceMax = 0;
            $netPriceMax = 0;
            $specialPriceMax = 0;
            foreach ($product->variants as $variant)
            {
                $priceMax = ($priceMax < $variant->price) ? $variant->price : $priceMax ;

                $netPriceMax = ($netPriceMax < $variant->net_price) ? $variant->net_price : $netPriceMax ;
                $specialPriceMax = ($specialPriceMax < $variant->special_price) ? $variant->special_price : $specialPriceMax ;
            }

            $priceMin = $priceMax;
            $netPriceMin = $netPriceMax;
            $specialPriceMin = $specialPriceMax;
            foreach ($product->variants as $variant)
            {
                $priceMin = ($priceMin > $variant->price) ? $variant->price : $priceMin ;

                $netPriceMin = ($netPriceMin > $variant->net_price) ? $variant->net_price : $netPriceMin ;
                $specialPriceMin = ($specialPriceMin > $variant->special_price) ? $variant->special_price : $specialPriceMin ;
            }

            $product->price_range = array(
                'max' => $priceMax,
                'min' => $priceMin
            );

            $product->net_price_range = array(
                'max' => $netPriceMax,
                'min' => $netPriceMin
            );

            $product->special_price_range = array(
                'max' => $specialPriceMax,
                'min' => $specialPriceMin
            );
        }

        $countItem = $products->count();
        $countTotalItem = $collection->products()->count();
        $countTotalPage = ceil($countTotalItem / $this->take);

        $responseData = array(
            'products'   => $products->toArray(),
            'page'       => $this->page,
            'total_page' => $countTotalPage,
            'item'       => $countItem,
            'total_item' => $countTotalItem
        );

        return API::createResponse($responseData);
    }

}