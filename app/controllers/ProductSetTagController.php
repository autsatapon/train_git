<?php

class ProductSetTagController extends AdminController {

    protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Tag', URL::to('products/set-tag'));
        //$this->theme->breadcrumb()->add('Set Product Tag', URL::to('products/set-tag'));
    }

    public function getIndex()
    {
     	/*
        $product = new ProductRepository();
		$products = $product->executeFormSearch()->with('brand','variants')->get();
        */
        $products = $this->product->executeFormSearch()->orderBy('title')->get();

        $products = $this->product->filterSearchResults($products);
        $products->load('brand','variants');

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

		$this->theme->setTitle('List Product');
		$view_data = compact('products');

        return $this->theme->of('products.set-tag.index', $view_data)->render();
    }

    public function getSearch()
    {
        /*
        $products = new ProductRepository();
        $products = $products->executeFormSearch()->with('variants')->get();
        */
        $products = $this->product->getExecuteFormSearch();
        $products->load('variants', 'brand');

        $this->theme->breadcrumb()->add('Product Search Result', URL::to('products/search'));
        $this->theme->setTitle('Product Search Result');

        $view_data = compact('products');

        return $this->theme->of('products.search', $view_data)->render();
    }

	public function getEdit($id)
    {
        // $productData = Product::findOrfail($id);
        $productData = $this->product->find($id);

        $this->data['product'] = $productData;

        $this->data['formData'] = array(
            'tag' => $productData->tag ,
        );

        $this->data['allTag'] = $this->getTag();

        $this->theme->breadcrumb()->add('Edit Tag', URL::to('product/set-tag/edit/'.$id));
        $this->theme->setTitle('Edit Tag');

        return $this->theme->of('products.set-tag.edit', $this->data)->render();
    }

	public function postEdit($id)
    {

        $product = $this->product->find($id);
        $data = Input::only('tag', 'brand');

        $rules['tag'] = array('required', "unique:products,tag,{$id}");
        $v = Validator::make($data, $rules);

        if ( $v->fails() )
        {
            return Redirect::back()->with( 'errors', $v->messages() )->withInput();
        }

        $this->postTag($data['tag']);
        //$tag = new Tag;
        $product->tag = $data['tag'] ;
        $product->save();

        return Redirect::back()->with( 'success', 'Product tag has been modified.');
	}

    public function getTag()
    {
        $tags =  Tag::all();
        $allTag = array();
        foreach ($tags as $tag) {
            $allTag[] = $tag->detail ;
        }

        return json_encode($allTag) ;
    }
    public function postTag($postTag=null)
    {
        $tagArray = explode(",", $postTag);
        $rules['detail'] = array('required', "unique:tags,detail");

        foreach ($tagArray as $tagValue) {

                $tagValue = trim($tagValue);
                $data = array('detail' => $tagValue);
                $v = Validator::make($data, $rules);
                if (!$v->fails())
                {
                    $tag = new Tag;
                    $tag->detail = $tagValue ;
                    $tag->save();
                }
        }
    }
}