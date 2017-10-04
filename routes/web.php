<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('register', 'Auth\RegisterController@showRegistrationForm');
Route::post('register', 'Auth\RegisterController@register');
Route::get('password/email', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout');

Route::get('home', function() {
    return Redirect::to('/');
});

Route::get('dealer-order-form', function() {
    return view('dealer-order-form');
});
Route::get('customer-order-form', function() {
    return view('customer-order-form');
});

Route::get('esign/errors', ['as' => 'order-esign-errors', 'uses' => 'EsignController@errors']);
Route::get('esign/thanks', ['as' => 'order-esign-thanks', 'uses' => 'EsignController@thanks']);
Route::post('esign-callback', ['as' => 'esign-callback', 'uses' => 'EsignController@callback']);

// lock to dealer role
Route::get('orders/{order_uuid}/initial-esign', ['as' => 'order-esign', 'uses' => 'OrdersController@initialEsignOrderDocument']);
Route::get('orders/{order_uuid}/esign-via-email', ['as' => 'order-esign-email', 'uses' => 'OrdersController@esignOrderDocumentViaEmail']);

// rto company
Route::get('orders/{order_uuid}/esign/{signature_id}', ['as' => 'esign-order-by-signature-id', 'uses' => 'OrdersController@esignOrderDocumentBySignatureId']);

Route::get('dealer-map', function() {
    return view('dealer-map');
});
Route::get('inventory_building/{inventory_building}', 'BuildingsController@pdfInitialInventoryForm');
Route::get('documents/price-list', 'DocumentsController@priceList');
Route::get('documents/order-form', 'DocumentsController@orderForm');
Route::get('documents/rto-docs', 'DocumentsController@rtoDocs');
Route::get('documents/promo99', 'DocumentsController@promo99');
Route::get('documents/building-configuration', 'DocumentsController@buildingConfiguration');
Route::get('documents/delivery-form', 'DocumentsController@deliveryForm');
Route::get('documents/neighbor-release', 'DocumentsController@neightborRelease');

Route::get('buildings/{building}/inventory-form', 'BuildingsController@pdfInventoryForm');

Route::get('/', 'HomeController@index');

Route::group(['middleware' => 'uscguard'], function () {

    Route::get('buildings/{building}/work-form', 'BuildingsController@pdfWorkForm')->name('building/work-form');
    Route::get('buildings/{building}/work-building-form', 'BuildingsController@pdfWorkBuildingForm')->name('building/work-building-form');
    Route::get('buildings/import', 'BuildingsController@import');
    Route::get('delivery-trucks', 'TrucksController@index');
    Route::get('delivery-trailers', 'TrailersController@index');

    Route::resource('buildings', 'BuildingsController');
    Route::resource('building-models', 'BuildingModelsController');
    Route::resource('building-statuses', 'BuildingStatusesController');
    Route::resource('building-packages', 'BuildingPackagesController');
    Route::resource('building-package-categories', 'BuildingPackagesCategoriesController');

    Route::resource('plants', 'PlantsController');
    Route::resource('styles', 'StylesController');
    Route::resource('options', 'OptionsController');
    Route::resource('locations', 'LocationsController');
    Route::resource('settings', 'SettingsController');
    Route::resource('colors', 'ColorsController');
    Route::resource('dealers', 'DealersController');
    Route::resource('deliveries', 'DeliveriesController');
    Route::resource('orders', 'OrdersController');
    Route::resource('sales', 'SalesController');
    Route::resource('price-groups', 'PriceGroupsController');
    Route::resource('employees', 'EmployeesController');
    // Route::resource('leads', 'LeadsController');
    Route::get('option-categories', 'OptionCategoriesController@index');

    Route::get('reports', 'ReportsController@index');
    Route::post('reports/ajax-report/{report_type}', 'ReportsController@ajaxReport');

    Route::get('bills/report', 'BillsController@report');
    Route::resource('bills', 'BillsController', ['only' => ['create', 'show', 'store', 'index']]);

    Route::get('expenses/report', 'ExpensesController@report');
    Route::resource('expenses', 'ExpensesController', ['only' => ['']]);
    Route::group(['prefix' => 'reports'], function() {
        Route::get('Chart', 'ReportsController@chartsData');
        Route::get('Chart/production', 'ReportsController@productionChartsData');
    });
    Route::get('print/{identifier}', 'PrintController@showprint');
});

Route::group(['middleware' => ['auth', 'qraccess'] ], function() {
    Route::get('qr-build/{id}','QrcodeController@qrBuildLocation');
    Route::get('qr-location/{id}','QrcodeController@qrBuildLocation');
    Route::resource('qrcode', 'QrcodeController');
});