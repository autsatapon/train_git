<?php

use Carbon\Carbon;

class TestController extends Controller {

    protected $product;
    protected $test;
    protected $order;

    public function __construct(ProductRepository $product, MessageRepository $message)
    {
        if (strtolower(App::environment()) === 'production')
        {
            return Redirect::to('/');
        }

        $this->test = rand(100, 50000);
        $this->product = $product;
        $this->message = $message;
    }

    public function getUp()
    {
        sd(Config::get('up::uploader'));
    }

    public function getLog()
    {
    	LOGGER('test-log', range(1,10));

    }

    public function getTestOrder()
    {
    	$wetrust = App::make('wetrust');

    	$order = Order::find(315);
    	$xml = $wetrust->buildXML($order);

    	echo $xml; die();
    }

    public function getPivot()
    {
    	$product = Product::with("styleTypes")->withTrashed()->find(2);
        $product->rebuildStyleTypeMediaSet();
    	// d($product);
    	// // exit;
    	// $pivot = $product->styleTypes->first()->pivot;
     //    $pivot->media_set = 1;
     //    // sd($pivot);
    	// $pivot->save();
        sd(DB::getQueryLog());
    	// // d($styleType);
    	// echo "444";
    }

	public function getArtisan()
	{
		$output = Artisan::call('command:cron', array('method' => 'updatePromotionProducts'));
		// $output = Artisan::call('flight-schedule:fetch', array('action' => 'airline'));
		var_dump($output);
	}

	public function getFixProduct()
	{
		$products = Product::with("variants.styleOptions", "styleOptions")->get();

		foreach ($products as $key => $product) {
			if ($product->variants->count() > 1)
			{
				$product->has_variants = 1;
			}
			else
			{
				$product->has_variants = 0;
			}

			$product->save();

			$variantStyleOptions = new Illuminate\Database\Eloquent\Collection;
	        foreach ($product->variants as $variant)
	        {
	            foreach ($variant->styleOptions as $key => $styleOption) {
	                $id = $styleOption->getKey();
	                if ($variantStyleOptions->find($id))
	                    continue;

	                $variantStyleOptions->add($styleOption);
	            }
	        }

	        $variantStyleOptionsID = $variantStyleOptions->lists('id');

	        $current = $product->styleOptions->lists('id');

	        // check style option from variant that don't have on product style option
	        // create it from template
	        foreach ($variantStyleOptionsID as $key => $id) {
	            if (! in_array($id, $current))
	            {
	                // this is neww!!! - get text and meta of style option
	                // and set it as default
	                $product->styleOptions()->attach($id, array(
	                    'text' => $styleOptions->find($id)->text,
	                    'meta' => $styleOptions->find($id)->meta,
	                ));
	            }
	        }

	        // check style option from product style option
	        // that don't have on variant so delete it
	        foreach ($product->styleOptions as $key => $styleOptions) {
	            if (! in_array($styleOptions->id, $variantStyleOptionsID))
	            {
	                $product->styleOptions()->detach($styleOptions->id);
	            }
	        }
		}
	}

	public function anyArm()
	{
       $theme = Theme::uses('admin')->layout('popup-iframe');

       // popup manage items plugin
       $theme->asset()->container('footer')->usePath()->add('popup-manage-items', 'admin/js/popup-manage-items.js', 'jquery');

       return $theme->of('test.arm')->render();
	}

   public function anyFg()
   {
	   $temp = @fopen("php://input", 'r');
	   $fg_response = urldecode(stream_get_contents($temp));
	   $fg_response = str_replace('xmlRes=', '', $fg_response);

	   s($fg_response);

	   $t = new Tmp;
	   $t->key = 'foreground-callback';
	   $t->value = $fg_response;
	   $t->save();
   }

   	public function anyRedis()
   	{
   		$goon = array('00001' => 'Test 0001');

   		$inventory_id = 38265;

   		$stock = Cache::tags("stock_{$inventory_id}")->get('stock');
   		Cache::tags("stock_{$inventory_id}")->put('stock', $goon, 5);

   		sd($stock);
   	}

	public function anyGoon()
	{
		s(Product::find(76)->variants);
		// d(date('y-m-j'));
		// $stock = App::make('StockRepositoryInterface');
		// sd($stock->checkRemainings(1, array(38265,38257,42278,42281,8584,40172,10654)));
		// $pickupItems = $stock->pickupItems(1, array(
			// '41550' => 3,
			// '8584' => 1,
			// '19234' => 2,
			// '40237' => 1,
			// '42293' => 1,
		// ));
		// d($pickupItems);

		// $order = Order::find(94);
		// d($stock->holdStock($order, $pickupItems));

		// d($stock->checkSCStock(array('41550','40172','42278','40237')));
		// d($stock->checkSCStock(array('19231','19234')));

		// d($stock->extendHoldStock($order));

		// d($stock->checkSCStock(array('19231','19234')));

		// d($stock->cutStock($order, rand(4000,9000)));

		// d($stock->checkSCStock(array('8584')));

		// $order = Order::findOrFail(94);

  //       $paymentRepo = App::make('PaymentRepositoryInterface');
  //       $paymentRepo->checkReconcile($order);

		// $repo = new SupplyChainRepository();
		// $repo->dailySync('2014-01-01 00:00', '2014-02-01 00:00');
		// $repo->syncLot('2014-03-01 00:00:00', '2014-03-13 23:59:59');
	}

	public function anyRequery($orderId)
	{
		$wetrust = App::make('wetrust');
		$paymentData = $wetrust->requery($orderId);

		sd($paymentData);
	}

	public function anyReconcile($orderId)
	{
		$order = Order::findOrFail($orderId);
		$order->order_status = 'draft';
		$order->save();

		$wetrust = App::make('wetrust');
		$paymentData = $wetrust->reconcile($order);

		sd($paymentData);
	}

	public function anyCutstock($orderId)
	{
		$order = Order::findOrFail($orderId);
		$order->order_status = 'draft';
		$order->save();

		$reconcileData = array(
			'ref1' => $order->ref1,
			'ref2' => $order->ref2,
			'ref3' => $order->ref3,
			'payment_order_id' => $order->payment_order_id
			);
//                echo '<pre>';
//                print_r($order->toArray());
//                echo "</pre>";
//                echo '<pre>';
//                print_r($reconcileData);
//                echo "</pre>";
//                die();
		$payment = App::make('PaymentRepositoryInterface');
		$payment->saveReconcile($order, $reconcileData);
	}

	public function getPermission()
	{
		if (Input::has('phpinfo'))
		{
			phpinfo();
			exit;
		}
	}

	public function doSomething($param, $param2)
	{
		d($this->test, $param, $param2);
	}

	public function anyJo()
	{
				// $cart = CartRepository::getCart(array(
		//	 Input::get('customer_ref_id'),
		//	 Input::get('customer_type')
		// ));
		$cart = Cart::find(66);

		// $order = Order::find(1);
		// PCMSPromotion::transfer($cart, $order);
		//
		$instance = PCMSPromotion::run($cart);
		$instances = $instance->getInstances();


		$cart->discount = 0;
		$cart->cartDetails->each(function($model)
			{
				$model->total_discount = 0;
			});



		// $response = Event::fire('onDeapplyingCode', array($cart, 'armtestcc'));
		// s($response);
		$response = Event::fire('Checkout.onCreatingOrder', $cart);
		s($response);
		d($cart);

		// exit;
		// Event::fire('Cart.onApplyCode', array($cart, 'armtestcc'));
		// d($instances[0]->getInstancePromotions());
		// exit;
		// $variant = ProductVariant::find(70);
		// s(PKeysRepository::prepareSingle($variant, 'parent')->get());
		// s(PKeysRepository::prepareSingle($variant, 'parent')->get());
		//
		// $cart = Cart::find(33);
		// $cart->setPromotionData('ccc');
		// $cart->addPromotionCode('data');
		// s($cart->toArray());
		// d($cart);
	}

	public function getSMS()
	{
		// $pay_date = date("d/m/Y เวลา H:i:s น.", strtotime('2014-04-02 9:34:40'.' -1 day'));
		// return $pay_date;
		// die();
		$number = Input::get('number');
		if(!empty($number))
		{
	        $sms = new SMS;
	        var_dump($sms->send($number, "Test Send SMS"));
		}

	}

	public function anyTum()
	{
		// return Config::get('email_template.front_url');
		// echo "<img src='http://itruemart-true.igetapp.com/themes/itruemart/assets/images/itruemart-logo.jpg' height='66'>";
		// echo HTML::image('/themes/admin/assets/images/itruemart-logo.jpg', 'itruemart', array('height'=>'66'));
    }

	public function getEmail()
	{

		$m = Input::get('mail');
		if(!empty($m))
		{
	        $email = new Email;
	        var_dump($email->send($m, "TEST SEND MAIL" , "TEST SEND MAIL", 'google'));
		}

		// echo preg_replace("/https?:\/\/(www)?\.youtube\.com\/watch\?v=([^&]+).*/", "$2", "http://www.youtube.com/watch?v=3T2FpCDlyNg");
		// @mkdir("uploads/barcode/2014-03-08");
		// @chmod("uploads/barcode/2014-03-08", 0777);
	}

	public function getSetVariant($productId)
	{
		$product = Product::with('variants')->findOrFail($productId);

		$viewData = compact('product');
		return Theme::uses('admin')->layout('admin-auth')->of('test.index', $viewData)->render();
	}

	public function getQuota($inventory_id = 1466)
	{
		$app_id = 2;

		$repo = new StockRepository();
		d($repo->pickup($app_id, $inventory_id, 5));

		/*
		  $quotaStocks = VariantQuota::where('inventory_id', $inventory_id)->where('app_id', $app_id)->get();

		  if( ! $quotaStocks->isEmpty() )
		  {
		  $stocks = VariantLot::where('inventory_id', $inventory_id)->whereNotIn('id', $quotaStocks->lists('variant_lot_id'))->get();
		  }
		  else
		  {
		  $stocks = VariantLot::where('inventory_id', $inventory_id)->get();
		  }

		  echo 'xxx';
		  d( $stocks->toArray(), $quotaStocks->toArray() );
		 */
	}

	public function getOrder()
	{
		$order = Order::find(2);
		$order->order_status = 'checked';
		$order->save();
	}

	public function getPromotion()
	{
		$theme = Theme::uses('admin')->layout('admin-auth');

		$theme->breadcrumb()->add('Manage Promotion', URL::to('promotions'));

		$theme->asset()->usePath()->add('promotions-create', 'admin/css/promotions_create.css', array('style-mws-style', 'style-mws-theme'));

		$theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
		$theme->asset()->container('footer')->usePath()->add('jquery-ui-timepicker', 'jui/js/timepicker/jquery-ui-timepicker.min.js', 'jquery');
		$theme->asset()->container('footer')->usePath()->add('promotions-create', 'admin/js/promotions_create.js', 'jquery');

		return $theme->of('promotions.test')->render();
	}

	public function getUpload()
	{
		echo Form::open(array('files' => true));
		echo Form::file('userfile');
		echo Form::submit();
		echo Form::close();
	}

	public function postUpload()
	{
		$destinationPath = '/home/vhosts/pcms/public/uploads/';

		//sd(Input::file('userfile')->getPathname());
		//s(move_uploaded_file(Input::file('userfile')->getPathname(), $destinationPath.'test222.jpg'));
		Input::file('userfile')->move($destinationPath, 'change2.jpg');
	}

	public function getIndex()
	{
		//d(URL::action("AuthController@getResetpw"));
	}

	public function getPcmskey()
	{
		$model = new PCMSKey;
		$model->vid = 1;
		$model->code = 12345;
		if ($model->save())
			echo 'Saved';
		else
			echo 'Cannot save';
	}

	public function getPcmsModel()
	{
		$model = new ProductVariant;
		$model->save();
	}

	public function getUserapp()
	{
		$user = User::find(8);
		d($user->apps()->get());
		//d(DB::getQueryLog());
	}

	public function getProduct()
	{
		$products = $this->product->search(null, 2);

		foreach ($products as $product)
		{
			echo $product->title;
		}
	}

	public function getTrueu()
	{
		$trueCard = App::make('truecard');

		$res = $trueCard->getInfoByThaiId('3101201287361')->check();

		s($res);

		$hasCard = $trueCard->getInfoByThaiId('3101201287361')->hasCard();

		s($hasCard);

		$isRed = $trueCard->getInfoByThaiId('3101201287361')->isRed();

		s($isRed);

		$isBlack = $trueCard->getInfoByThaiId('3101201287361')->isBlack();

		s($isBlack);
	}

	// public function getEmail()
	// {
	// 	$Email = new Email;
	// 	var_dump($Email->send('siyingui@gmail.com', 'Subject Goon', 'Detail Goon', 'google', array('cc' => 'siyingui+test@gmail.com', 'bcc' => 'four_chong@hotmail.com')));
	// }

	public function getAddStock($inventoryId)
	{
		VariantLot::where('inventory_id', $inventoryId)->update(array(
			'sc_remaining' => 999,
		));

		Cache::tags("stock-$inventoryId")->flush();
	}

//   public function getAllAddress()
//   {
//       $provinces = Province::with('cities.districts')->where('id', 1)->get();
//
//       return API::createResponse($provinces->toArray());
//   }

    public function getDay()
    {
        $h = App::make('HolidayRepositoryInterface');

        sd($h->getFromNow());
    }

    public function getRc4()
    {
        $rc4 = '702acdbd795ca35ea8a96ac1ad32c545353758f82ea23d854346711bdf60ffea9a8d94e78ff1c5bb26fb1c9d86717dde8cb6b3c35f28dcff4bd704f2b45a28a42e0e3eede14241b312f48b2dea4254d19b6693e0b90d1ecfeefbd8638e5b040287e467a15f299a94b1eec10ea46626b4427b3dee4ba99d5f7102eb30ce18d29759053fed1af28652b26c9c5c40e1c85a22d26c88e6a025ab74cf41608f6cd47360edfe1d3a602f12df7ae105db5b649824f9c577719282af3c554bf0f14eae776d20967d649c60f7f88438e561b5836ec4d9a3d508211fcef9b4a2fb3bf00d2ced7a242dfc423812fcab95e118eef98e6cbb48a1db71ea896f4be137545bed10063a079e759e891d5230ef7ee327c659359ad28162ad26d35b643c42b29a87ca4b1f854cbaa836b5393e0396e5eab69a540dd9cc1d7f9049bb59ac5fca24f15f3c955c9416f57ff37a8aa238491c57c3e73d4d7820dcc04345970eb4f5ac8d9cda576b47c062d621c8a5f72a749547cc4f81';
        $wt = App::make('wetrust');

        sd($wt->BGCallbackResponse($rc4));
    }

    public function getRebuildPromotion()
    {
    	$promotion = Promotion::find(Input::get('id', 179));
    	$promotion->rebuildPromotion();

    	sd(DB::getQueryLog());
    }


    /**
     * prototype test case of pkey repository - pls don't remove it.
     */
    public function getTestPkeyRepo()
    {
    	$models = array(
    		"Collection" => Collection::with('products.variants'),
    		"Product" => Product::with('variants'),
    		"Brand" => Brand::with('products.variants')
    		);
    	foreach ($models as $modelName => $model) {
            $skip = 0;

            // skip to brand that has product
            if ($modelName == 'Brand')
            {
                $skip = 2;
            }

    		$parentCollection = $model->take(1)->skip($skip)->get();
    		$parentModel = $parentCollection->first();

    		$excludeProduct = $excludeVariant = array();
    		// d($parentModel);
    		// sd($parentModel->product);
    		if (! empty($parentModel->products) && $parentModel->products->first())
    		{
    			foreach($parentModel->products as $product)
    			{
    				if ($product->variants->count() > 0)
    				{
    					$excludeProduct = array($product->pkey);
    				}
    			}

    		}

    		// sd($excludeProduct);

    		if (! empty($parentModel->variants) && $parentModel->variants->first())
    		{
    			$excludeVariant = array($parentModel->variants->first()->pkey);
    		}

            $pkey = \PKeysRepository::prepare($parentCollection, 'child')
                    ->get();

    		$pkeyExcluded = \PKeysRepository::prepare($parentCollection, 'child')
                    ->setExclude('product', $excludeProduct)
                    ->setExclude('variant', $excludeVariant)
                    ->get();

            foreach ($pkey as $key => $p) {
            	$pkey[$key] = ProductVariant::whereIn('pkey', $p)->lists('pkey');
            }

            foreach ($pkeyExcluded as $key => $p) {
            	$pkeyExcluded[$key] = ProductVariant::whereIn('pkey', $p)->lists('pkey');
            }

            d($modelName, $pkey, $pkeyExcluded, $excludeProduct, $excludeVariant);
    	}
    }

    public function getFlushCache()
    {
    	echo 'Flush all cache section.';

    	Cache::tags('product')->flush();
    	Cache::tags('products')->flush();
    	Cache::tags('brand')->flush();
    	Cache::tags('brands')->flush();
    	Cache::tags('collection')->flush();
    	Cache::tags('collections')->flush();
    }

    public function getFlushStockCache($inventory_ids)
    {
    	$explode = explode(',', $inventory_ids);
    	if ($explode)
    	{
    		foreach ($explode as $inventory_id)
    		{
    			Cache::tags("stock_{$inventory_id}")->flush();
    		}
    	}
    }

    public function getTranslateVariant()
    {
        $product = Product::find(Input::get('id', 13));
        // d($product);
        // exit;

        $product->rebuildVariantsTitle();

        sd(DB::GetQueryLog());
    }

    public function getPappCache()
   {
       $papp = App::make('PAppRepositoryInterface');

//       $papp->purgeCacheByPkey('45311375168544');

       sd($papp->getByPkey('45311375168544')->toArray());
   }

    public function getRedis()
    {
    	sd(Cache::get('test-xxxxx'));
    }

}

// $product = Product::find(1);

// Promo::type($product)->build();

// class Promo {

// 	protected $type;

// 	protected $model;

// 	public function type(Model $model)
// 	{
// 		$this->type = get_class($model);

// 		$this->model = $model;
// 	}

// 	public function build()
// 	{
// 		$promotions = Promotion::get()->filter(function($data)
// 		{
// 			return isset($data->condtions->trueyou);
// 		});

// 	}

// }

// $promotion = Promotion::find(47);

// $which = ucfirst($promotion->effects->which);

// $ids = $promotion->effects->following_items;

// $which::with(
// 	array(
// 		'products' => function($q)
// 		{
// 			$q->whereNotIn();
// 		},
// 		'products.variants' => function($q)
// 		{
// 			$q->whereNotIn();
// 		}
// 	)
// )->whereIn('id', $ids)->get();
