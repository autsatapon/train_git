<?php
class ApiXController extends ApiBaseController {

	 public function __construct()
    {
	
	}
	
	public function getIndex()
    {
		//echo 1;
		$brandIdArr = Product::where('status', 'publish')->lists('brand_id');
		 $brands = Brand::whereIn('id', $brandIdArr)->orderBy('name', 'asc')->get();
		//sd($brands);
		
		$visibleBrandFields = array('pkey', 'name');
        foreach ($brands as $brand)
        {
            $brand->setVisible($visibleBrandFields);
        }
		
		//return API::createResponse(FALSE, 404);
        return API::createResponse($brands->toArray());
	}
   
   public function getCache()
   {
       $papp = App::make('PAppRepositoryInterface');
       
       sd($papp->getByPkey('45311375168544'));
   }
}