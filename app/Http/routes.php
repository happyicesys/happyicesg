<?php

//debug purpose
/*Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});*/

get('/', function () {
    return view('errors.503');
});

Route::get('/admin', function () {
    return view('welcome');
});

get('/position/data', 'PositionController@getData');
delete('/position/data/{id}', 'PositionController@destroyAjax');
resource('position', 'PositionController');

post('/person/{person_id}/note', 'PersonController@storeNote');
get('/person/specific/data/{person_id}', 'PersonController@getPersonData');
get('/person/price/{person_id}', 'PersonController@personPrice');
post('person/addaccessory/{person_id}', 'PersonController@addAccessory');
delete('person/addaccessory/{addaccessory_id}', 'PersonController@removeAccessory');
post('person/addfreezer/{person_id}', 'PersonController@addFreezer');
delete('person/addfreezer/{addfreezer_id}', 'PersonController@removeFreezer');
get('/person/profile/{person_id}', 'PersonController@getProfile');
get('/person/log/{person_id}', 'PersonController@generateLogs');
get('person/transac/{person_id}', 'PersonController@showTransac');
get('/person/data', 'PersonController@getData');
delete('/person/data/{id}', 'PersonController@destroyAjax');
resource('person', 'PersonController');
post('person/{id}/file', 'PersonController@addFile');
delete('person/{id}/file', 'PersonController@removeFile');

get('/profile/data', 'ProfileController@getData');
resource('profile', 'ProfileController');
// resource('sale', 'SaleController');

get('/item/data', 'ItemController@getData');
delete('/item/data/{id}', 'ItemController@destroyAjax');
resource('item', 'ItemController');

resource('price', 'PriceController');

get('/transaction/person/latest/{person_id}', 'TransactionController@showPersonTransac');
get('/transaction/status/{transaction_id}', 'TransactionController@changeStatus');
post('/transaction/daterange', 'TransactionController@searchDateRange');
post('/transaction/log/{trans_id}', 'TransactionController@generateLogs');
get('/transaction/download/{trans_id}', 'TransactionController@generateInvoice');
get('/transaction/data', 'TransactionController@getData');
delete('/transaction/data/{id}', 'TransactionController@destroyAjax');
post('/transaction/{trans_id}/editpersoncode', 'TransactionController@storeCustcode');
put('/transaction/{trans_id}/editperson', 'TransactionController@storeCust');
put('/transaction/{trans_id}/totalqty', 'TransactionController@storeTotalQty');
put('/transaction/{trans_id}/total', 'TransactionController@storeTotal');
get('/transaction/person/{person_id}', 'TransactionController@getCust');
get('/transaction/item/{person_id}', 'TransactionController@getItem');
get('/transaction/person/{person_id}/item/{item_id}', 'TransactionController@getPrice');
resource('transaction', 'TransactionController');

delete('/deal/data/{id}', 'DealController@destroyAjax');
get('/deal/data/{transaction_id}', 'DealController@getData');
resource('deal', 'DealController');

get('/user/data', 'UserController@getData');
delete('/user/data/{id}', 'UserController@destroyAjax');
resource('user', 'UserController');

get('/role/data', 'RoleController@getData');
resource('role', 'RoleController');

get('/report', 'RptController@index');
post('/report/person', 'RptController@generatePerson');
post('/report/transaction', 'RptController@generateTransaction');

get('/freezer/data', 'FreezerController@getData');
delete('/freezer/data/{id}', 'FreezerController@destroyAjax');
resource('freezer', 'FreezerController');

get('/accessory/data', 'AccessoryController@getData');
delete('/accessory/data/{id}', 'AccessoryController@destroyAjax');
resource('accessory', 'AccessoryController');

get('/payterm/data', 'PaytermController@getData');
delete('/payterm/data/{id}', 'PaytermController@destroyAjax');
resource('payterm', 'PaytermController');

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');