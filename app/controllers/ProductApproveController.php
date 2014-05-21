<?php

class ProductApproveController extends AdminController {

    protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Product Approve', URL::to('products/approve'));
        // $this->theme->breadcrumb()->add('Product Approve', URL::to('products/approve'));
    }


    public function getIndex()
    {
        // $products = $this->product->getExecuteFormSearch();
        $products = $this->product->hasModified()->executeFormSearch()->orderBy('title')->get();
        $products = $this->product->filterSearchResults($products);
        $products->load('brand', 'variants', 'revisions');

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        // sd($products->count());

        // $revisionCount = 0;
        // $products->each(function($product) use (&$revisionCount){
        //     foreach ($product->revisions as $key=>$revision) {
        //         if ( $revision->status != 'publish')
        //         {
        //             $revisionCount++;
        //         }
        //     }
        // });

        // $products = Paginator::make($products->slice($skip, $perPage)->all(), $revisionCount, $perPage);

        $this->theme->setTitle('Product List');
        $this->data['products'] = $products;
        return $this->theme->of('products.approve.index', $this->data)->render();
    }

    public function getWaitForPublish()
    {
        $products = $this->product->hasModified()->executeFormSearch()->with(array('revisions' => function($q){ $q->where('status', 'approved'); }))->orderBy('title')->get();
        $products = $this->product->filterSearchResults($products);
        $products->load('brand', 'variants');


        // $page = Input::get('page') ?: 1;
        // $perPage = 10;
        // $skip = $perPage * ($page-1);

        // $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

        // d($products); die();

        $this->theme->setTitle('Product List');
        $this->data['products'] = $products;
        return $this->theme->of('products.approve.wait-for-publish', $this->data)->render();
    }

    public function getDetail($id, $revisionId)
    {
        $this->theme->breadcrumb()->add('Modified Detail', '#');

        $product = $this->product->find($id);
        $product->load('brand', 'variants', 'revisions');

        $revision = $product->revisions()->find($revisionId);
        // $editData = json_decode($revision->value, TRUE);
        $modifiedData = $revision->modified_data;
        $modifiedFields = array_keys($modifiedData);
        $modifiedValues = array_values($modifiedData);

        $this->data['revision'] = $revision;
        $this->data['modifiedData'] = $modifiedData;

        $this->theme->setTitle('Modified Detail');
        $this->data['product'] = $product;
        return $this->theme->of('products.approve.detail', $this->data)->render();
    }

    public function postDetail($id, $revisionId)
    {
        $product = $this->product->find($id);
        $product->load('revisions', 'collections');

        if (Input::get('status') == 'approved' && $product->collections->count() < 1)
        {
            return Redirect::back()->withErrors("You must assign collection to this product before approve.");
        }

        $revision = $product->revisions->find($revisionId);
        $revision->status = Input::get('status');
        $revision->note = Input::get('note');
        $revision->save();

        return Redirect::to('products/approve');
    }






    public function getPublish($id, $revisionId)
    {

        $this->theme->breadcrumb()->add('Modified Detail', 'products/approve');


        $product = $this->product->find($id);
        $product->load('brand', 'variants', 'collections','mediaContents','variants.mediaContents');
        
        $revision = $product->revisions()->find($revisionId);

        $editData = json_decode($revision->value, TRUE);

        $modifiedData = $revision->modified_data;
        $modifiedFields = array_keys($modifiedData);
        $modifiedValues = array_values($modifiedData);

        $this->data['revision'] = $revision;
        $this->data['modifiedData'] = $modifiedData;

        $this->theme->setTitle('Publish Product');
        $this->data['product'] = $product;
        return $this->theme->of('products.approve.publish', $this->data)->render();
    }

    public function postPublish($id, $revisionId)
    {
        $product = $this->product->find($id);
        $revision = $product->revisions->find($revisionId);

        // Save Publish.
        $this->product->savePublishProduct($id, $revisionId);

        return Redirect::to('products/approve/wait-for-publish');
    }

}