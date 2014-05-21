<?php

class ProductCollectionController extends AdminController {

	protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Product', URL::to('products'));
        $this->theme->breadcrumb()->add('Manage Product and Collection', URL::to('products/collection'));
    }

	public function getIndex()
	{
		// $products = $this->product->executeFormSearch()->hasStatus('publish')->get();
		$products = $this->product->executeFormSearch()->orderBy('title')->get();

        $products = $this->product->filterSearchResults($products);
		$products->load('collections', 'mediaContents');

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

		$this->data['products'] = $products;

		$this->theme->setTitle('Manage Product and Collection');
        return $this->theme->of('products.collection.index', $this->data)->render();
	}

	public function getInsert()
	{
		$productIdArr = Input::get('product');
		$checkedAll = FALSE;

		if (!empty($productIdArr))
		{
			// $products = Product::hasStatus('publish')->whereIn('id', $productIdArr)->get();
			$products = Product::whereIn('id', $productIdArr)->get();
			$checkedAll = TRUE;
		}
		else
		{
			// $products = Product::hasStatus('publish')->get();
			$products = array();//Product::get();
			$checkedAll = FALSE;
		}

		$rootCollections = Collection::rootCollection()->get();
		$rootCollections->load(implode('.', array_fill(0, 20, 'children')));

		$this->data['rootCollections'] = $rootCollections;
		$this->data['products'] = $products;
		$this->data['checkedAll'] = $checkedAll;
      
      // popup manage items plugin
       $this->theme->asset()->container('footer')->usePath()->add('popup-manage-items', 'admin/js/popup-manage-items.js', 'jquery');

		$this->theme->setTitle('Insert Product to Collection');

		$this->theme->breadcrumb()->add('Insert Product to Collection', URL::to('products/collection/insert'));
		return $this->theme->of('products.collection.insert', $this->data)->render();
	}

	public function postInsert()
	{
		$productIdArr = Input::get('product');
		$collectionIdArr = Input::get('collection', array());

		// $products = Product::with('collections')->hasStatus('publish')->whereIn('id', $productIdArr)->get();
		$products = Product::with('collections')->whereIn('id', $productIdArr)->get();

		foreach ($products as $key => $product)
		{
			ElasticUtils::removeProduct($product);

			if ( !$product->collections->isEmpty() )
			{
				$collectionIds = array_merge($collectionIdArr, $product->collections->lists('id'));
			}
			else
			{
				$collectionIds = $collectionIdArr;
			}

			$product->collections()->sync($collectionIds);

			$product->load('collections');

			ElasticUtils::updateProduct($product);
		}

		return Redirect::to('products/collection');
	}

	public function getSet($productId)
	{
		$product = Product::with('collections')->find($productId);

		$rootCollections = Collection::rootCollection()->get();
		$rootCollections->load(implode('.', array_fill(0, 20, 'children')));

		$this->data['product'] = $product;
		$this->data['rootCollections'] = $rootCollections;

		$this->theme->setTitle('Set Product Collection');

		$this->theme->breadcrumb()->add('Set Product Collection', URL::to('products/collection/set/'.$productId));
		return $this->theme->of('products.collection.set', $this->data)->render();
	}

	public function postSet($productId)
	{
		$product = Product::with('collections')->find($productId);
		$collectionIdArr = Input::get('collection');

		ElasticUtils::removeProduct($product);

		if (!empty($collectionIdArr))
		{
			$product->collections()->sync($collectionIdArr);
		}
		else
		{
			// $product->collections()->detach();
            return Redirect::back()->withErrors('You must select at least one collection to this product.');
		}

		$product->load('collections');

		ElasticUtils::updateProduct($product);

		if (Input::has('return-collection'))
			return Redirect::to('collections/products/'.Input::get('return-collection'));
		return Redirect::to('products/collection');
	}

}