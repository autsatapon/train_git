<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

/* controllers (No Authentication Required) */
// Route::controller('migrate', 'DataMigrationController');
Route::controller('test', 'TestController');
Route::controller('tontest', 'TonTestController');
Route::controller('search-test', 'SearchTestController');
Route::group(array('prefix' => 'command'), function()
{
    Route::get('/', 'CommandController@getIndex');
    Route::post('/', 'CommandController@postStore');
    Route::get('/call', 'CommandController@getCall');
    Route::get('/run', 'CommandController@getRun');
});

Route::controller('supplychain', 'SupplyChainController');
Route::controller('payment', 'PaymentController');

/*Krucamper migrate itruemart*/
Route::controller('migrate-itruemart', 'MigrationItruemartController');
Route::controller('migrate-products', 'MigrateProductsController');
Route::controller('migrate-members', 'MigrateMembersController');
Route::controller('migrate-supplychain', 'MigrateSupplychainController');
Route::controller('migrate-flashsale', 'MigrateFlashsaleController');
Route::controller('migrate-order', 'MigrateOrderController');
Route::controller('migrate-promotions', 'MigratePromotionsController');

Route::controller('migrate-shops', 'MigrateShopsController');

/* Run Migrate On Production (Brand and Collection) */
Route::controller('migrate', 'MigrateController');



/* Admin Authentication Route */
/*
  Route::get('logout',  array('as' => 'auth.logout',      'uses' => 'AuthController@getLogout'));
  Route::get('login',   array('as' => 'auth.login',       'uses' => 'AuthController@getLogin'));
  Route::post('login',  array('as' => 'auth.login.post',  'uses' => 'AuthController@postLogin'));
  Route::get('signup',   array('as' => 'auth.signup',       'uses' => 'AuthController@getSignup'));
  Route::post('signup',  array('as' => 'auth.signup.post',  'uses' => 'AuthController@postSignup'));
 */
// cart/{method}/{app_Id}/{user_id}/{item}
Route::controllers(array(
    'auth'  => 'AuthController',
    'check' => 'CheckController'
));


Route::group(array('prefix' => 'api/{pkey}', 'before' => 'valid.api'), function()
{
    /* Checkout API */
    Route::controller('checkout', 'ApiCheckoutController');

    /* Product Search API */
    Route::get('products/search', 'ApiProductSearchController@searchResults');

    /* Products API */
    Route::resource('products', 'ApiProductsController');
    Route::get('inventories/{inventoryId}/remaining', 'ApiProductsController@checkRemaining');
    Route::get('variants/{variantKey}', 'ApiProductsController@getVariantByPkey');
    Route::get('inventories/{inventoryId}', 'ApiProductsController@getVariantByInventoryId');

    /* Get Real Pkey */
    Route::get('products/real-pkey/{pkeyOrItmId}', 'ApiProductsController@getRealPkey');
    Route::get('collections/real-pkey/{pkeyOrItmId}', 'ApiCollectionsController@getRealPkey');
    Route::get('brands/real-pkey/{pkeyOrItmId}', 'ApiBrandsController@getRealPkey');

    /* Orders API */
    Route::controller('orders', 'ApiOrdersController');

    /* Otps API */
    Route::get('otps/request', 'ApiOtpsController@getRequest');
    Route::get('otps/validate', 'ApiOtpsController@getValidate');

    /* Collections API */
    Route::get('collections/flash-sale', 'ApiCollectionsController@getFlashsaleCollections');
    Route::get('collections/itruemart-tv', 'ApiCollectionsController@getItruemartTvCollections');
    Route::get('collections/discount', 'ApiCollectionsController@getdiscountCollections');
    Route::get('collections/trueyou', 'ApiCollectionsController@getTrueyouCollections');
    Route::get('collections/{collectionKey}/products', 'ApiCollectionsController@getListProducts');
    Route::get('collections/{collectionKey}/brands', 'ApiCollectionsController@getListBrands');
    Route::get('collections/{collectionKey}/bestseller', 'ApiCollectionsController@getListBestseller');
    Route::get('collections/{collectionKey}/products/vendor', 'ApiCollectionsVendorController@getListProducts');
    Route::get('collections/{collectionKey}', 'ApiCollectionsController@getByPkey');
    Route::get('collections', 'ApiCollectionsController@getIndex');

    /* Brands API */
    Route::get('brands/flash-sale', 'ApiBrandsController@getFlashsaleBrands');
    Route::get('brands/itruemart-tv', 'ApiBrandsController@getItuemartTvBrands');
    Route::get('brands/discount', 'ApiBrandsController@getDiscountBrands');
    Route::get('brands/trueyou', 'ApiBrandsController@getTrueyouBrands');
    Route::get('brands/{brandKey}/products', 'ApiBrandsController@getListProducts');
    Route::get('brands/{brandKey}', 'ApiBrandsController@getByPkey');
    Route::get('brands', 'ApiBrandsController@getIndex');

    /* Cart API */
    Route::controller('cart', 'ApiCartController');

    /* Payment API */
    Route::controller('payment', 'ApiPaymentController');

    /* Credit Card API */
    Route::controller('credit-card', 'ApiCreditCardController');

    /* Credit Card Payment API */
    Route::controller('credit-card-payment', 'ApiCreditCardPaymentController');

    /* Provinces API */
    Route::controller('provinces', 'ApiProvincesController');

    /* lastlogin API */
    Route::get('lastlogin/{ssoId}', 'ApiLastloginController@getLastlogin');

    /* Shop Data API */
    Route::get('shop/{shop_id}', 'ApiShopController@getShop');

    /* Cities API */
    Route::controller('cities', 'ApiCitiesController');

    /* Districts API */
    Route::controller('districts', 'ApiDistrictsController');

    /* Zipcode API */
    Route::controller('zipcodes', 'ApiZipcodeController');

    /* Customers API */
    Route::controller('customers', 'ApiCustomersController');

    /* Customer Addresses API */
    Route::controller('customerAddresses', 'ApiCustomerAddressesController');

    /* Members API */
    Route::controller('members', 'ApiMembersController');

    /* Fix API */
    Route::controller('fix', 'ApiFixController');

    /* Ton Test API */
    Route::get('ton/products/{productPkey}', 'ApiTonController@product');
    Route::get('ton/collections/{collectionKey}/products', 'ApiTonController@listCollectionProducts');

	/* Banners API */
	Route::controller('banners', 'ApiBannersController');

    /* Elastic Search API */
    Route::controller('elastic-search', 'ApiElasticSearchController');

});

/* Route Group - Authentication */
Route::group(array('before' => 'auth.admin'), function()
{
    Route::controllers(array(
        'dashboard'                  => 'DashboardController',
        'roles'                      => 'GroupsController',
        'users'                      => 'UsersController',
        //'tontest'                  => 'TonTestController',
        'brands'                     => 'BrandsController',
        'policies/brands'            => 'PoliciesBrandsController',
        'policies/vendors'           => 'PoliciesVendorsController',
        'policies/assigns'           => 'PolicyAssignmentsController',
        'policies'                   => 'PoliciesController',
        'shops'                      => 'ShopsController',
        'apps'                       => 'AppsController',
        'collections'                => 'CollectionsController',
        'concept'                    => 'ConceptController',
        'discount-campaigns'         => 'DiscountCampaignsController',
        'products/approve'           => 'ProductApproveController',
        'products/set-content'       => 'ProductContentController',
        'products/set-variant'       => 'ProductSetVariantController',
        'products/set-variant-style' => 'ProductSetVariantStyleController',
        'products/set-price'         => 'ProductSetPriceController',
        'products/new-material'      => 'ProductNewMaterialController',
        'products/search'            => 'ProductSearchController',
        'products/set-shipping'      => 'ProductSetShippingController',
        'products/set-tag'           => 'ProductSetTagController',
        'products/collection'        => 'ProductCollectionController',
        'products'                   => 'ProductsController',
        'variants'                   => 'VariantsController',
        'campaigns'                  => 'CampaignsController',
        'promotions'                 => 'PromotionsController',
        'orders'                     => 'OrdersController',
        'shipping/delivery-area'     => 'ShippingDeliveryAreaController',
        'shipping/set-method'        => 'ShippingSetMethodController',
        'shipping/method'            => 'ShippingMethodController',
        'shipping/boxes'             => 'ShippingBoxesController',
        'shipping/payment-methods'   => 'PaymentMethodsController',
        'shipping'                   => 'ShippingController',
        'banners/positions'          => 'BannerPositionController',
        'banners/groups'             => 'BannerGroupController',
		'banners'					 => 'BannersController',
        'metas'                      => 'MetasController',
        'holidays'                      => 'HolidaysController',
    ));

    Route::get('/', 'DashboardController@getIndex');
});

Route::bind('pkey', function($pkey)
{
    $papp = App::make('PAppRepositoryInterface');

//    $app = PApp::where('pkey', $pkey)->remember(360)->first();
    $app = $papp->getByPkey($pkey);

    PApp::setCurrentApp($app);

    return $app;
});

Route::filter('valid.api', function($route, $request)
    {
        $app = $route->getParameter('pkey');

        if (!$app)
        {
            return API::createResponse("Application was not found.", 404);
        }
    });

//#### Tok Test Controller
Route::controller('toktest', 'TokTestController');
Route::get('mail', 'TestController@getEmail');
Route::get('sms', 'TestController@getSMS');
Route::get('tum', 'TestController@anyTum');
Route::get('tbd', 'TestController@getEmailTemplate');




/* Elastic Search */
Route::group(array('prefix' => 'api-search'), function()
{
    /* API Indexing */
    Route::any('indexing/{index}', array(
        'as' => 'index.store',
        'uses' => 'ApiIndexingController@store'
    ));

    Route::any('indexing/{index}/{type}', array(
        'as' => 'index.map',
        'uses' => 'ApiIndexingController@map'
    ));


    /* API Document */
    Route::delete('document/{index}/{type}/{id}', array(
        'as' => 'doc.destroy',
        'uses' => 'ApiDocumentController@destroy'
    ));

    Route::put('document/{index}/{type}/{id}', array(
        'as' => 'doc.update',
        'uses' => 'ApiDocumentController@update'
    ));

    Route::post('document/{index}/{type}', array(
        'as' => 'doc.store',
        'uses' => 'ApiDocumentController@store'
    ));

    /* API Search */
    Route::any('search/{index}/{types?}', array(
        'as' => 'search.basic',
        'uses' => 'ApiSearchController@index'
    ));

    Route::any('search/{index}/{types}/{id}', array(
        'as' => 'search.find',
        'uses' => 'ApiSearchController@find'
    ));
});