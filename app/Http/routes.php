<?php

//debug purpose
/*Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});*/

get('/', function () {
    return view('client.index');
});

Route::get('/admin', function () {
    return view('welcome');
});

get('/market/setup/price', 'MarketingController@indexSetupPriceApi');
post('/market/setup/price', 'MarketingController@storeSetupPrice');
get('/market/setup', 'MarketingController@indexSetup');
post('/market/customer/{customer_id}', 'MarketingController@updateCustomer');
get('/market/customer/{customer_id}/edit', 'MarketingController@editCustomer');
post('/market/customer', 'MarketingController@storeCustomer');
post('/market/batchcustomer', 'MarketingController@storeBatchCustomer');
get('/market/customer/create', 'MarketingController@createCustomer');
get('/market/customer/batchcreate', 'MarketingController@createBatchCustomer');
get('/market/customer/data', 'MarketingController@indexCustomerApi');
get('/market/customer', 'MarketingController@indexCustomer');
get('/market/member/{member_id}/edit', 'MarketingController@editMember');
post('/market/member/{member_id}', 'MarketingController@updateMember');
post('/market/member', 'MarketingController@storeMember');
post('/market/member/self/{self_id}', 'MarketingController@updateSelf');
get('/market/member/create/{level}', 'MarketingController@createMember');

post('/market/deal/index', 'MarketingController@indexDealApi');
post('/market/deal/{dtdtransaction_id}', 'MarketingController@update');
get('/market/dealData/{dtdtransaction_id}', 'MarketingController@getDealData');
get('/market/deal/{dtdtransaction_id}/edit', 'MarketingController@editDeal');
get('/market/deal/latest/{person_id}', 'MarketingController@showDtdTransaction');
post('market/deal', 'MarketingController@storeDeal');
get('/market/deal/create', 'MarketingController@createDeal');
get('/market/deal/{dtdtransaction_id}', 'MarketingController@showDeal');
get('/market/deal', 'MarketingController@indexDeal');
get('/market/deal/download/{trans_id}', 'MarketingController@generateInvoice');
get('/market/deal/emailInv/{dtd_transaction_id}', 'MarketingController@sendEmailInv');
post('/market/deal/reverse/{dtd_transaction_id}', 'MarketingController@reverse');
delete('market/deal/cancel/{id}', 'MarketingController@destroy');
delete('/market/deal/data/{id}', 'MarketingController@destroyAjax');

get('/market/member/data', 'MarketingController@indexMemberApi');
get('/market/member', 'MarketingController@indexMember');
get('/market/docs', 'MarketingController@indexDocs');
resource('market', 'MarketingController');

resource('onlineprice', 'OnlinePriceController');

post('inventory/email', 'InventoryController@invEmailUpdate');
get('inventory/email', 'InventoryController@invEmail');
post('inventory/setting', 'InventoryController@invLowest');
get('inventory/setting', 'InventoryController@invIndex');
get('/inventory/item/{inventory_id}', 'InventoryController@itemInventory');
delete('/inventory/data/{id}', 'InventoryController@destroyAjax');
get('/inventory/data', 'InventoryController@getData');
resource('inventory', 'InventoryController');

get('/client/item', 'ClientController@clientProduct');
get('/client/register', 'ClientController@getRegister');
get('/client/about', 'ClientController@getAboutUs');
get('/client/product', 'ClientController@getProduct');
get('/client/contact', 'ClientController@getContact');
post('/client/contact', 'ClientController@sendContactEmail');
resource('client', 'ClientController');

get('/position/data', 'PositionController@getData');
delete('/position/data/{id}', 'PositionController@destroyAjax');
resource('position', 'PositionController');

get('/person/user/{user_id}', 'PersonController@getPersonUserId');
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

post('/item/{item_id}/photo', 'ItemController@addImage');
get('/item/image/{item_id}', 'ItemController@imageItem');
delete('/item/image/{item_id}', 'ItemController@destroyImageAjax');
post('/item/image/{item_id}', 'ItemController@editCaption');
get('/item/data', 'ItemController@getData');
delete('/item/data/{id}', 'ItemController@destroyAjax');
resource('item', 'ItemController');

resource('price', 'PriceController');

post('/transaction/rpt/{trans_id}', 'TransactionController@rptDetail');
post('/transaction/reverse/{trans_id}', 'TransactionController@reverse');
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

get('/user/data/{user_id}', 'UserController@getUser');
get('/user/data', 'UserController@getData');
delete('/user/data/{id}', 'UserController@destroyAjax');
resource('user', 'UserController');
get('/user/member/{user_id}/{level}', 'UserController@convertInitD');

get('/role/data', 'RoleController@getData');
resource('role', 'RoleController');

post('/report/dailyrpt/verify', 'RptController@getVerifyPaid');
post('/report/dailypdf', 'RptController@getDailyPdf');
post('/report/dailyrec', 'RptController@generateDailyRec');
post('/report/dailyrpt', 'RptController@getDailyRptApi');
get('/report', 'RptController@index');
post('/report/person', 'RptController@generatePerson');
post('/report/transaction', 'RptController@generateTransaction');
post('/report/deal', 'RptController@generateByProduct');
post('/report/driver', 'RptController@generateDriver');

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