<?php

//debug purpose
/*Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});*/

Route::get('/', function () {
    return view('welcome');
});

get('/position/data', 'PositionController@getData');
delete('/position/data/{id}', 'PositionController@destroyAjax');
resource('position', 'PositionController');

get('/person/data', 'PersonController@getData');
delete('/person/data/{id}', 'PersonController@destroyAjax');
resource('person', 'PersonController');
post('person/{id}/file', 'PersonController@addFile');
delete('person/{id}/file', 'PersonController@removeFile');

resource('profile', 'ProfileController');
// resource('sale', 'SaleController');

get('/item/data', 'ItemController@getData');
delete('/item/data/{id}', 'ItemController@destroyAjax');
resource('item', 'ItemController');

resource('price', 'PriceController');

get('/transaction/data', 'TransactionController@getData');
delete('/transaction/data/{id}', 'TransactionController@destroyAjax');
put('/transaction/{trans_id}/editperson', 'TransactionController@storeCust');
get('/transaction/person/{person_id}', 'TransactionController@getCust');
get('/transaction/item/{person_id}', 'TransactionController@getItem');
get('/transaction/person/{person_id}/item/{item_id}', 'TransactionController@getPrice');
resource('transaction', 'TransactionController');

get('/deal/data/{transaction_id}', 'DealController@getData');
delete('/deal/data/{id}', 'DealController@destroyAjax');
resource('deal', 'DealController');

get('/user/data', 'UserController@getData');
delete('/user/data/{id}', 'UserController@destroyAjax');
resource('user', 'UserController');

get('/role/data', 'RoleController@getData');
resource('role', 'RoleController');


// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');