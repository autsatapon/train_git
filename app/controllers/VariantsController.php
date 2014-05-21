<?php
class VariantsController extends AdminController {

    protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Variant Management', URL::to('Variants'));
    }

 	 public function getIndex()
    {
     	/*
        $product = new ProductRepository();
		$products = $product->executeFormSearch()->with('brand','variants')->get();
        */
		

        $products = $this->product->getExecuteFormSearch();
        $products->load('brand','variants','styleTypes');

		$this->theme->setTitle('Variant Management');

		$view_data = compact('products');
        return $this->theme->of('variants.index', $view_data)->render();
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

    public function getCreate()
    {

    }

    public function postCreate()
    {

    }

    public function getEdit($id = 0)
    {
   
        $products = ProductVariant::find($id); 
        $this->theme->setTitle('Variant Management');
        
        $thisthis = $this->listStyleOption();


        $view_data = compact('products','thisthis');
       // sd($view_data) ;
        return $this->theme->of('variants.edit',$view_data)->render();
	}

    public function listStyleOption(){

        $thisArray = array();
        $StyleTypes = StyleType::all() ;
        foreach ($StyleTypes as  $value) {
            $thisArray[$value->id] =  $this->listOption($value->id) ;           
        }

        return $thisArray ;

    }

    public function listOption($id){
     return $VariantStyleOptions = VariantStyleOption::where('style_type_id',$id)->groupBy('text')->get();
    }
    public function postEdit($id)
    {
        sd($_POST);
        exit();
    }

	public function getDelete($id)
    {
		$variant = ProductVariant::find($id);
		$del = $variant->delete();

        $success = 'variant has been deleted.';

        return Redirect::to('/variants')->with('success', $success);
    }


}