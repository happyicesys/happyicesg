<?php

Route::get('/', function () {
    return view('client.index');
});

Route::get('/admin', function () {
    return view('welcome');
});
Route::get('/api/d2donlinesales', 'D2dOnlineSaleController@allApi');
Route::get('/api/d2ditems/{covered}', 'D2dOnlineSaleController@allItems');
Route::post('/api/validateOrder', 'D2dOnlineSaleController@validateOrder');
Route::post('/api/submitOrder', 'D2dOnlineSaleController@submitOrder');

Route::post('/api/postcodes/excel', 'PostcodeController@exportPostcode');
Route::get('/api/postcodes/all', 'PostcodeController@allPostcodesApi');
Route::post('/postcode/verify', 'PostcodeController@verifyPostcode');

Route::post('/market/dtdtrans/index', 'MarketingController@getIndexDtdCustTrans');
Route::post('/market/setup/postcode/update', 'MarketingController@updatePostcodeForm');
Route::get('/market/setup/d2ditem/create', 'MarketingController@createDtdOnlineItems');
Route::post('/market/setup/d2ditem', 'MarketingController@storeDtdOnlineItem');
Route::get('/market/setup/d2ditem/{id}/edit', 'MarketingController@editDtdOnlineItem');
Route::post('/market/setup/d2ditem/{id}', 'MarketingController@updateDtdOnlineItem');
Route::delete('/market/setup/d2ditem/{id}', 'MarketingController@destroyDtdOnlineItem');
Route::get('/market/setup/members', 'MarketingController@getAllMembers');
Route::get('/market/setup/postcodes', 'MarketingController@getPostcodes');
Route::post('/market/setup/postcode', 'MarketingController@storePostcode');
Route::get('/market/setup/price', 'MarketingController@indexSetupPriceApi');
Route::post('/market/setup/price', 'MarketingController@storeSetupPrice');
Route::get('/market/setup', 'MarketingController@indexSetup');
Route::post('/market/customer/transfer', 'MarketingController@transferCustomer');
Route::post('/market/customer/{customer_id}', 'MarketingController@updateCustomer');
Route::get('/market/customer/{customer_id}/edit', 'MarketingController@editCustomer');
Route::get('/market/customer/notify/{customer_id}', 'MarketingController@notifyManagerIndex');
Route::post('/market/customer/notify/{customer_id}', 'MarketingController@storeNotification');
Route::delete('/market/customer/notify/{notification_id}', 'MarketingController@destroyNotification');
Route::post('/market/customer', 'MarketingController@storeCustomer');
Route::post('/market/batchcustomer', 'MarketingController@storeBatchCustomer');
Route::get('/market/customer/create', 'MarketingController@createCustomer');
Route::get('/market/customer/batchcreate', 'MarketingController@createBatchCustomer');
Route::get('/market/customer/batchtransfer', 'MarketingController@transferBatchCustomer');
Route::get('/market/customer/data', 'MarketingController@indexCustomerApi');
Route::get('/market/customer/emaildraft', 'MarketingController@emailDraft');
Route::post('/market/customer/update/emaildraft', 'MarketingController@updateEmailDraft');
Route::get('/market/customer', 'MarketingController@indexCustomer');
Route::delete('/market/customer/{member_id}', 'MarketingController@destroyCustomer');
Route::get('/api/market/customer/{member_id}', 'MarketingController@getDescendantCustomer');
Route::get('/market/member/{member_id}/edit', 'MarketingController@editMember');
Route::post('/market/member/{member_id}', 'MarketingController@updateMember');
Route::post('/market/member', 'MarketingController@storeMember');
Route::post('/market/member/self/{self_id}', 'MarketingController@updateSelf');
Route::get('/market/member/create/{level}', 'MarketingController@createMember');
Route::delete('/market/member/{member_id}', 'MarketingController@destroyMember');
Route::get('/api/market/exclude/descmember', 'MarketingController@getDescMembersExcept');

Route::get('/market/commision/create', 'MarketingController@createCommision');

Route::post('market/deal/commision', 'MarketingController@storeCommision');
Route::post('/market/log/deal/{dtdtransaction_id}', 'MarketingController@generateLogs');
Route::post('/market/deal/index', 'MarketingController@indexDealApi');
Route::post('/market/deal/{dtdtransaction_id}', 'MarketingController@update');
Route::get('/market/dealData/{dtdtransaction_id}', 'MarketingController@getDealData');
Route::get('/market/deal/{dtdtransaction_id}/edit', 'MarketingController@editDeal');
Route::get('/market/deal/latest/{person_id}', 'MarketingController@showDtdTransaction');
Route::get('/market/deal/commision/latest/{person_id}', 'MarketingController@showDtdCommision');
Route::post('market/deal', 'MarketingController@storeDeal');
Route::get('/market/deal/create', 'MarketingController@createDeal');
Route::get('/market/deal/create/commision', 'MarketingController@createCommision');
Route::get('/market/deal/{dtdtransaction_id}', 'MarketingController@showDeal');
Route::get('/market/deal', 'MarketingController@indexDeal');
Route::get('/market/deal/download/{trans_id}', 'MarketingController@generateInvoice');
Route::get('/market/deal/emailInv/{dtd_transaction_id}', 'MarketingController@sendEmailInv');
Route::post('/market/deal/reverse/{dtd_transaction_id}', 'MarketingController@reverse');
Route::delete('market/deal/cancel/{id}', 'MarketingController@destroy');
Route::delete('/market/deal/data/{id}', 'MarketingController@destroyAjax');

Route::get('/market/member/data', 'MarketingController@indexMemberApi');
Route::get('/market/member', 'MarketingController@indexMember');
Route::get('/market/docs', 'MarketingController@indexDocs');
Route::resource('market', 'MarketingController');

Route::resource('onlineprice', 'OnlinePriceController');

Route::post('inventory/email', 'InventoryController@invEmailUpdate');
Route::get('inventory/email', 'InventoryController@invEmail');
Route::post('inventory/setting', 'InventoryController@invLowest');
Route::get('inventory/setting', 'InventoryController@invIndex');
Route::get('/inventory/item/{inventory_id}', 'InventoryController@itemInventory');
Route::delete('/inventory/data/{id}', 'InventoryController@destroyAjax');
Route::get('/inventory/data', 'InventoryController@getData');
Route::resource('inventory', 'InventoryController');

Route::get('/franchise', 'ClientController@franchiseIndex');
Route::post('/franchise', 'ClientController@franchiseInquiry');
Route::get('/recruitment', 'ClientController@recruitmentIndex');
Route::get('/vending/funv', 'ClientController@funVendingIndex');
Route::get('/vending/honestv', 'ClientController@honestVendingIndex');
Route::post('/vending/funv', 'ClientController@funVendingInquiry');
Route::post('/vending/honestv', 'ClientController@honestVendingInquiry');
Route::get('/d2d', 'ClientController@d2dIndex');
Route::post('/d2d/email', 'ClientController@emailOrder');
Route::get('/client/item', 'ClientController@clientProduct');
Route::get('/client/register', 'ClientController@getRegister');
Route::get('/client/about', 'ClientController@getAboutUs');
Route::get('/client/product', 'ClientController@getProduct');
Route::get('/client/contact', 'ClientController@getContact');
Route::post('/client/contact', 'ClientController@sendContactEmail');
Route::resource('client', 'ClientController');

Route::get('/position/data', 'PositionController@getData');
Route::delete('/position/data/{id}', 'PositionController@destroyAjax');
Route::resource('position', 'PositionController');

Route::get('/person/replicate/{person_id}', 'PersonController@replicatePerson');
Route::get('/person/user/{user_id}', 'PersonController@getPersonUserId');
Route::post('/person/{person_id}/note', 'PersonController@storeNote');
Route::get('/person/specific/data/{person_id}', 'PersonController@getPersonData');
Route::get('/person/price/{person_id}', 'PersonController@personPrice');
Route::post('person/addaccessory/{person_id}', 'PersonController@addAccessory');
Route::delete('person/addaccessory/{addaccessory_id}', 'PersonController@removeAccessory');
Route::post('person/addfreezer/{person_id}', 'PersonController@addFreezer');
Route::delete('person/addfreezer/{addfreezer_id}', 'PersonController@removeFreezer');
Route::get('/person/profile/{person_id}', 'PersonController@getProfile');
Route::get('/person/log/{person_id}', 'PersonController@generateLogs');
Route::post('person/transac/{person_id}', 'PersonController@showTransac');
Route::get('/person/data', 'PersonController@getData');
Route::post('/api/people', 'PersonController@getPeopleApi');
Route::delete('/person/{id}', 'PersonController@destroy');
Route::delete('/person/data/{id}', 'PersonController@destroyAjax');
Route::resource('person', 'PersonController');
Route::post('person/{id}/file', 'PersonController@addFile');
Route::delete('person/{id}/file', 'PersonController@removeFile');
Route::get('/api/members/select', 'PersonController@getMemberSelectApi');

Route::get('/profile/data', 'ProfileController@getData');
Route::resource('profile', 'ProfileController');

Route::get('/item/qtyorder/{item_id}', 'ItemController@getItemQtyOrder');
Route::post('/api/item/qtyorder/{item_id}', 'ItemController@getItemQtyOrderApi');
Route::post('/api/item/unitcost', 'ItemController@getUnitcostIndexApi');
Route::post('/item/{item_id}/photo', 'ItemController@addImage');
Route::get('/item/image/{item_id}', 'ItemController@imageItem');
Route::delete('/item/image/{item_id}', 'ItemController@destroyImageAjax');
Route::post('/item/image/{item_id}', 'ItemController@editCaption');
Route::get('/item/data', 'ItemController@getData');
Route::delete('/item/data/{id}', 'ItemController@destroyAjax');
Route::post('/item/batchupdate/unitcost', 'ItemController@batchUpdateUnitcost');
Route::resource('item', 'ItemController');

Route::resource('price', 'PriceController');

Route::get('/transaction/emailInv/{trans_id}', 'TransactionController@sendEmailInv');
Route::post('/transaction/rpt/{trans_id}', 'TransactionController@rptDetail');
Route::post('/transaction/reverse/{trans_id}', 'TransactionController@reverse');
Route::get('/transaction/person/latest/{person_id}', 'TransactionController@showPersonTransac');
Route::get('/transaction/status/{transaction_id}', 'TransactionController@changeStatus');
Route::post('/transaction/singlestatus/{transaction_id}', 'TransactionController@changeSingleStatus');
Route::post('/transaction/daterange', 'TransactionController@searchDateRange');
Route::post('/transaction/log/{trans_id}', 'TransactionController@generateLogs');
Route::get('/transaction/download/{trans_id}', 'TransactionController@generateInvoice');
Route::post('/transaction/data', 'TransactionController@getData');
Route::delete('/transaction/data/{id}', 'TransactionController@destroyAjax');
Route::post('/transaction/{trans_id}/editpersoncode', 'TransactionController@storeCustcode');
Route::put('/transaction/{trans_id}/editperson', 'TransactionController@storeCust');
Route::put('/transaction/{trans_id}/totalqty', 'TransactionController@storeTotalQty');
Route::put('/transaction/{trans_id}/total', 'TransactionController@storeTotal');
Route::get('/transaction/person/{person_id}', 'TransactionController@getCust');
Route::get('/transaction/item/{person_id}', 'TransactionController@getItem');
Route::get('/transaction/person/{person_id}/item/{item_id}', 'TransactionController@getPrice');
Route::resource('transaction', 'TransactionController');

Route::delete('/deal/data/{id}', 'DealController@destroyAjax');
Route::get('/deal/data/{transaction_id}', 'DealController@getData');
Route::resource('deal', 'DealController');

Route::get('/user/data/{user_id}', 'UserController@getUser');
Route::get('/user/data', 'UserController@getData');
Route::delete('/user/data/{id}', 'UserController@destroyAjax');
Route::resource('user', 'UserController');
Route::get('/user/member/{user_id}/{level}', 'UserController@convertInitD');

Route::get('/role/data', 'RoleController@getData');
Route::resource('role', 'RoleController');

Route::post('/report/dailyrpt/verify/{id?}', 'RptController@getVerifyPaid');
Route::post('/report/dailypdf', 'RptController@getDailyPdf');
Route::post('/report/dailyrec', 'RptController@generateDailyRec');
Route::post('/report/dailyrpt', 'RptController@getDailyRptApi');
Route::get('/report', 'RptController@index');
Route::post('/report/person', 'RptController@generatePerson');
Route::post('/report/transaction', 'RptController@generateTransaction');
Route::post('/report/deal', 'RptController@generateByProduct');
Route::post('/report/driver', 'RptController@generateDriver');

Route::match(['get', 'post'], '/detailrpt/invbreakdown/detail', 'DetailRptController@getInvoiceBreakdownDetail');
Route::get('/detailrpt/invbreakdown/summary', 'DetailRptController@getInvoiceBreakdownSummary');
Route::post('/api/detailrpt/invbreakdown/summary', 'DetailRptController@getInvoiceBreakdownSummaryApi');
Route::get('/detailrpt/account', 'DetailRptController@accountIndex');
Route::post('/api/detailrpt/account/custdetail', 'DetailRptController@getAccountCustdetailApi');
Route::post('/api/detailrpt/account/outstanding', 'DetailRptController@getAccountOutstandingApi');
Route::post('/api/detailrpt/account/paydetail', 'DetailRptController@getAccountPaydetailApi');
Route::post('/api/detailrpt/account/paysummary', 'DetailRptController@getAccountPaysummaryApi');
Route::post('/detailrpt/account/paysummary', 'DetailRptController@submitPaySummary');
Route::get('/detailrpt/sales', 'DetailRptController@salesIndex');
Route::post('/api/detailrpt/sales/custdetail', 'DetailRptController@getSalesCustdetailApi');
Route::post('/api/detailrpt/sales/custsummary', 'DetailRptController@getSalesCustSummaryApi');
Route::post('/api/detailrpt/sales/productday', 'DetailRptController@getSalesProductDetailDayApi');
Route::post('/api/detailrpt/sales/productmonth', 'DetailRptController@getSalesProductDetailMonthApi');
Route::get('/detailrpt/sales/{item_id}/thismonth', 'DetailRptController@getProductDetailMonthThisMonth');
Route::post('/api/detailrpt/sales/thismonth/{item_id}', 'DetailRptController@getProductDetailMonthThisMonthApi');


Route::get('/freezer/data', 'FreezerController@getData');
Route::delete('/freezer/data/{id}', 'FreezerController@destroyAjax');
Route::resource('freezer', 'FreezerController');

Route::get('/accessory/data', 'AccessoryController@getData');
Route::delete('/accessory/data/{id}', 'AccessoryController@destroyAjax');
Route::resource('accessory', 'AccessoryController');

Route::get('/payterm/data', 'PaytermController@getData');
Route::delete('/payterm/data/{id}', 'PaytermController@destroyAjax');
Route::resource('payterm', 'PaytermController');

Route::get('/custcat/data', 'CustcategoryController@getData');
Route::delete('/custcat/data/{id}', 'CustcategoryController@destroyAjax');
Route::resource('custcat', 'CustcategoryController');

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// password reset route
Route::get('password/reset', 'Auth\AuthController@getPasswordReset');
Route::post('password/reset', 'Auth\AuthController@resetPassword');