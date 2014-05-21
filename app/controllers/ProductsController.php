<?php

class ProductsController extends AdminController {

    protected $product;
    protected $shippingMethod;

    public function __construct(
    ProductRepositoryInterface $product, ShippingMethodRepositoryInterface $shippingMethod
    )
    {
        parent::__construct();

        $this->product = $product;

        $this->shippingMethod = $shippingMethod;

        $this->theme->breadcrumb()->add('Product', URL::to('products'));
    }

    public function getIndex()
    {
        /*
          $product = new ProductRepository();
          $products = $product->executeFormSearch()->with('brand','variants')->get();
         */
        $products = $this->product->getExecuteFormSearch();
        $products->load('brand','variants','mediaContents');

        // list all inventory_id to get stock remaining
        $inventoryIds = array();
        $products->each(function($p) use (&$inventoryIds)
            {
                $p->variants->each(function($v) use (&$inventoryIds)
                    {
                        $inventoryIds[] = $v->inventory_id;
                    });
            });

        // get remainings
        $stock = App::make('StockRepositoryInterface');
        $remainings = $stock->getSCStock(implode(',', $inventoryIds));

        $page = Input::get('page') ? : 1;
        $perPage = 10;
        $skip = $perPage * ($page - 1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

        // d($products); die();

        $this->theme->setTitle('List Product');

        $view_data = compact('products', 'remainings');

        return $this->theme->of('products.index', $view_data)->render();
    }

    public function getSearch()
    {
        /*
          $products = new ProductRepository();
          $products = $products->executeFormSearch()->with('variants')->get();
         */
        $products = $this->product->getExecuteFormSearch();
        $products->load('brand','variants','mediaContents');

        $this->theme->breadcrumb()->add('Product Search Result', URL::to('products/search'));
        $this->theme->setTitle('Product Search Result');

        $view_data = compact('products');

        return $this->theme->of('products.search', $view_data)->render();
    }

    public function getEdit($id)
    {
        // $productData = Product::findOrfail($id);
        $product = $this->product->find($id);

        $apps = PApp::with(array('metas' => function($q)
                {
                    $q->where('model', 'Product');
                }))
            ->get();

        $user = Sentry::getUser();

        $productRevisionsOfUser = $product->revisions()->whereIn('status', array('draft', 'rejected', 'approved'))->where('editor_id', $user->id)->get();

        $this->data['product'] = $product;
        $this->data['revisions'] = $productRevisionsOfUser;
        $this->data['apps'] = $apps;

        $this->data['formData'] = array(
            'title' => $product->title
        );

        $this->theme->breadcrumb()->add('Edit Product', URL::to('product/edit/'.$id));
        $this->theme->setTitle('Edit Product');

        return $this->theme->of('products.edit', $this->data)->render();
    }

    public function postEdit($id)
    {
        /*
          // $product = Product::with('brand')->findOrfail($id);
          $product = $this->product->find($id);
          $product->load('brand');

          $product->title = Input::get('title');

          //sd($product);
          if (Input::get('translate'))
          {
          $translate = Input::get('translate');
          $product->setTranslate('title', $translate['title']);
          }

          if ( ! $product->save())
          {
          return Redirect::back()->with( 'errors', $product->errors())->withInput();
          }

          return Redirect::back()->with( 'success', 'Product name has been modified.');
         */

        $product = $this->product->find($id);

        /* Validate Title before Save Draft */
        /* Because when Save Draft, It is not auto validate (by Harvey) */
        $draftData = Input::only('title', 'brand_id', 'active');
        if (empty($draftData['brand_id']))
        {
            $draftData['brand_id'] = $product->brand->id;
        }

        $rules['title'] = array('required', "unique:products,title,{$id}");

        $v = Validator::make($draftData, $rules);
        if ($v->fails())
        {
            return Redirect::back()->with('errors', $v->messages())->withInput();
        }
        /* Set Translate */
        if (Input::has('translate'))
        {
            $translate = Input::get('translate');
            $product->setTranslate('title', $translate['title']);

            $product->save();
        }

        // check dirty active
        if ($product->active != Input::get('active'))
        {
            $product->active = Input::get('active');
            $product->save();

            if ($product->active == '1')
            {
                ElasticUtils::updateProduct($product);
            }
            else
            {
                ElasticUtils::removeProduct($product);
            }
        }
        else
        {
            /* Save Draft */
            $this->product->saveDraft($id, $draftData);
        }

        return Redirect::back()->with('success', 'Product name has been modified.');
    }

    public function postRemove($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->title;

        $product->delete();

        return Redirect::to('/products')->with('success', '"'.$name.'" was moved to trash.');
    }

    public function getTrash()
    {
        $products = Product::onlyTrashed()->where('status', '!=', 'incomplete')->get();

        $this->theme->setTitle('Deleted Product');

        $view_data = compact('products');

        return $this->theme->of('products.trashed', $view_data)->render();
    }

    public function getRestore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        $product->restore();

        return Redirect::to('/products')->with('success', 'Product has been restored.');
    }

}

