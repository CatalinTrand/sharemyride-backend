<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/verifyCaptcha', function (Request $request) {
    $secret = "6LcTZpoUAAAAAM8xeVfNOsTH1bab4AVRc8MF0txa";
    $site = "https://www.google.com/recaptcha/api/siteverify";

    $client = new GuzzleHttp\Client();
    return $client->get($site, ['secret' =>  $secret, 'recaptcha_challenge_field' => $request->recaptcha_challenge_field, 'recaptcha_response_field' => $request->recaptcha_response_field ]);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('feed', 'HomepageController@generateFeed');
Route::get('homepage/data', 'HomepageController@getHomeData');
Route::get('carlist/data', 'HomepageController@getListData');
Route::get('brandlist/data', 'HomepageController@getAllBrandsData');
Route::get('brand-carlist/data', 'HomepageController@getCategoryListData');
Route::get('product-data', 'VehicleController@show');
Route::get('offers/frontend-slider', 'HomepageController@getSliderOffers');
Route::get('offers/top-offers', 'OfferController@topOffers');
Route::post('offers/send-form', 'OfferController@sendFormData');
Route::get('home/counts', 'HomepageController@countsByAlimentazione');
Route::get('dashboard/stats', 'DashboardController@get_statistics');

//shop
Route::get('transaction-data', 'TransactionController@show');
Route::post('payment/card', 'TransactionController@cardPayment');

Route::group(['middleware' => ['jwt-auth', 'api-header']], function () {

    Route::post('findPassengersForRoute', 'MapRoutingController@findPassengers');
    Route::post('savePassengerLocation', 'MapRoutingController@savePassengerLocation');
    Route::post('notifyPassengers', 'MapRoutingController@notifyPassengers');
    Route::post('sendMessage', 'MapRoutingController@sendMessage');
    Route::post('getMessages', 'MapRoutingController@getMessages');

    // all routes to protected resources are registered here
    Route::get('users/list', function() {
        $users = App\User::all();

        $response = ['success' => true, 'data' => $users];
        return response()->json($response, 201);
    });

    Route::get('transactions', 'TransactionController@index');
    Route::get('transactions/process', 'TransactionController@markAsProcessed');

    Route::get('brands', 'BrandController@index');
    Route::get('brands/{id}', 'BrandController@show');
    Route::post('brands/create', 'BrandController@store');
    Route::post('brands/update', 'BrandController@update');
    Route::post('brands/update-logo', 'BrandController@updateLogo');
    Route::post('brand/update-visibility', 'BrandController@update_brand_visibility');
    Route::delete('brands/delete/{id}', 'BrandController@destroy');

    Route::get('vehicles', 'VehicleController@index');
    Route::get('vehicles/bybrand/{id}', 'VehicleController@get_vehicles_by_brand');
    Route::get('vehicles/{id}', 'VehicleController@showId');
    Route::post('vehicles/create', 'VehicleController@store');
    Route::post('vehicles/update', 'VehicleController@update');
    Route::delete('vehicles/delete/{id}', 'VehicleController@destroy');

    Route::get('variations', 'VariationController@index');
    Route::get('variations/byvehicle/{id}', 'VariationController@get_variations_by_vehicle');
    Route::get('variations/{id}', 'VariationController@show');
    Route::get('short-variations', 'VariationController@shortVariations');
    Route::get('short-variations-selects', 'VariationController@shortVariationsForSelects');
    Route::post('variations/create', 'VariationController@store');
    Route::post('variations/update', 'VariationController@update');
    Route::delete('variations/delete/{id}', 'VariationController@destroy');

    Route::get('optionals', 'OptionalController@index');
    Route::get('optionals/{id}', 'OptionalController@show');
    Route::get('optionals/for-offer/{id}', 'OptionalController@for_offer');
    Route::post('optionals/create', 'OptionalController@store');
    Route::post('optionals/update', 'OptionalController@update');
    Route::delete('optionals/delete/{id}', 'OptionalController@destroy');

    Route::get('tabs', 'TabController@index');
    Route::get('tabs/{id}', 'TabController@show');
    Route::get('tabs/for-offer/{id}', 'TabController@for_offer');
    Route::post('tabs/link-offer-tabs', 'TabController@link_offer_tabs');
    Route::post('tabs/create', 'TabController@store');
    Route::post('tabs/update', 'TabController@update');
    Route::post('tab/update-visibility', 'TabController@update_tab_visibility');
    Route::delete('tabs/delete/{id}', 'TabController@destroy');

    Route::get('tags', 'OffersTagsController@index');
    Route::get('tags/{id}', 'OffersTagsController@show');
    Route::post('tags/create', 'OffersTagsController@store');
    Route::post('tags/update', 'OffersTagsController@update');
    Route::delete('tags/delete/{id}', 'OffersTagsController@destroy');

    Route::get('offers', 'OfferController@index');
    Route::get('offers/slider', 'OfferController@index_slider');
    Route::get('offers/{id}', 'OfferController@show');
    Route::post('offers/create', 'OfferController@store');
    Route::post('offers/update', 'OfferController@update');
    Route::post('offers/swapPlacesInSlider', 'OfferController@swapPlacesInSlider');
    Route::delete('offers/delete/{id}', 'OfferController@destroy');

    Route::get('/check-auth', 'UserController@isAuthenticated');

    Route::post('homeslider/update', 'HomepageController@update_slider');
});

Route::group(['middleware' => 'api-header'], function () {
    // The registration and login requests doesn't come with tokens
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them

    Route::post('user/login', 'UserController@login');
    Route::post('user/register', 'UserController@register');
});
