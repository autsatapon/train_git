<?php

class ShippingSetMethodController extends AdminController {

    protected $shippingMethod;

    public function __construct(ShippingMethodRepositoryInterface $shippingMethod)
    {
        parent::__construct();

        $this->shippingMethod = $shippingMethod;

        $this->theme->breadcrumb()->add('Shipping', URL::to('shipping'));
        $this->theme->breadcrumb()->add('Set Shipping Method', URL::to('shipping/set-method'));
    }

    public function getIndex()
    {

    }

    public function getStock()
    {
        $shippingMethods = ShippingMethod::all();

        $typeStock = StockShippingMethod::where('stock_type', 'stock')->lists('shipping_method_id');
        $typeNonStock = StockShippingMethod::where('stock_type', 'non-stock')->lists('shipping_method_id');

        $this->data['shippingMethods'] = $shippingMethods;
        $this->data['typeStock'] = $typeStock;
        $this->data['typeNonStock'] = $typeNonStock;

        $this->theme->setTitle('Set Shipping Method by Stock Type');
        $this->theme->breadcrumb()->add('Set Shipping Method by Stock Type', URL::to('shipping/set-method/stock'));

        return $this->theme->of('shipping.set-method.stock', $this->data)->render();
    }

    public function postStock()
    {
        $typeStock = Input::get('shipping_method_id.stock');
        $typeNonStock = Input::get('shipping_method_id.nonstock');

        // Delete All Record.
        DB::table('stock_shipping_methods')->delete();

        $data = array();
        if (!empty($typeStock))
        {
            foreach ($typeStock as $key=>$val)
            {
                $data[] = array('stock_type' => 'stock', 'shipping_method_id' => $val);
            }
        }

        if (!empty($typeNonStock))
        {
            foreach ($typeNonStock as $key=>$val)
            {
                $data[] = array('stock_type' => 'non-stock', 'shipping_method_id' => $val);
            }
        }

        // Insert New Record
        DB::table('stock_shipping_methods')->insert($data);

        return Redirect::to('shipping/set-method/stock')->withSuccess('Set Shipping Method by Stock Type Complete.');
    }

    public function getVendor($vendorId = 0)
    {
        if ($vendorId == 0)
        {
            return $this->listVendors();
        }
        else
        {
            return $this->editVendor($vendorId);
        }
    }

    public function postVendor($vendorId = 0)
    {
        $vendor = VVendor::where('vendor_id', $vendorId)->first();

        if (empty($vendor))
        {
            return Redirect::to('shipping/set-method/vendor');
        }

        // Delete All Record.
        DB::table('vendor_shipping_methods')->where('vendor_id', $vendorId)->delete();

        $shippingMethodIdArr = Input::get('shipping_method_id');

        $data = array();
        if (!empty($shippingMethodIdArr))
        {
            foreach ($shippingMethodIdArr as $key=>$val)
            {
                $data[] = array('vendor_id' => $vendorId, 'shipping_method_id' => $val);
            }

            // Insert New Record
            DB::table('vendor_shipping_methods')->insert($data);
        }

        return Redirect::to('shipping/set-method/vendor');
    }

    protected function listVendors()
    {
        $vendors = VVendor::with('methods')->get();
        $this->data['vendors'] = $vendors;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Set Shipping Method by Vendor');
        $this->theme->breadcrumb()->add('Set Shipping Method by Vendor', URL::to('shipping/set-method/vendor'));

        return $this->theme->of('shipping.set-method.vendor', $this->data)->render();
    }

    protected function editVendor($vendorId)
    {
        $vendor = VVendor::with('methods')->where('vendor_id', $vendorId)->first();

        if (empty($vendor))
        {
            return Redirect::to('shipping/set-method/vendor');
        }

        $arrVendorShippingMethodId = $vendor->methods->lists('id');
        $shippingMethods = ShippingMethod::all();

        $this->data['arrVendorShippingMethodId'] = $arrVendorShippingMethodId;
        $this->data['shippingMethods'] = $shippingMethods;
        $this->data['vendor'] = $vendor;

        $this->theme->setTitle('Set Shipping Method by Vendor');
        $this->theme->breadcrumb()->add('Set Shipping Method by Vendor', URL::to('shipping/set-method/vendor'));
        $this->theme->breadcrumb()->add('Set Method', URL::to('shipping/set-method/vendor/'.$vendorId));

        return $this->theme->of('shipping.set-method.vendor-edit', $this->data)->render();
    }



    public function getProduct($productId = 0)
    {
        if ($productId == 0)
        {
            return $this->listProducts();
        }
        else
        {
            return $this->editProduct($productId);
        }
    }

    public function postProduct($productId = 0)
    {
        $product = Product::with('methods')->findOrFail($productId);

        // Delete All Record.
        DB::table('product_shipping_methods')->where('product_id', $productId)->delete();

        $shippingMethodIdArr = Input::get('shipping_method_id');

        $data = array();
        if (!empty($shippingMethodIdArr))
        {
            foreach ($shippingMethodIdArr as $key=>$val)
            {
                $data[] = array('product_id' => $productId, 'shipping_method_id' => $val);
            }

            // Insert New Record
            DB::table('product_shipping_methods')->insert($data);
        }

        $product->touch();

        return Redirect::to('shipping/set-method/product');
    }

    protected function listProducts()
    {
        $products = Product::with(array('methods', 'brand'))->get();
        $this->data['products'] = $products;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->setTitle('Set Shipping Method by Product');
        $this->theme->breadcrumb()->add('Set Shipping Method by Product', URL::to('shipping/set-method/product'));

        return $this->theme->of('shipping.set-method.product', $this->data)->render();
    }

    protected function editProduct($productId)
    {
        $product = Product::with('methods')->findOrFail($productId);

        $arrProductShippingMethodId = $product->methods->lists('id');
        $shippingMethods = ShippingMethod::all();

        $this->data['arrProductShippingMethodId'] = $arrProductShippingMethodId;
        $this->data['shippingMethods'] = $shippingMethods;
        $this->data['product'] = $product;

        $this->theme->setTitle('Set Shipping Method by Product');
        $this->theme->breadcrumb()->add('Set Shipping Method by Product', URL::to('shipping/set-method/product'));
        $this->theme->breadcrumb()->add('Set Method', URL::to('shipping/set-method/product/'.$productId));

        return $this->theme->of('shipping.set-method.product-edit', $this->data)->render();
    }
}