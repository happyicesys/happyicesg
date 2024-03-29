<?php

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    // Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}
// auth()->loginUsingId(100131);
Route::get('/', function ()
{
	return view('auth.login');

	// $url = request()->url();
	// if (strpos($url, 'my') !== false) {
	// 	return view('auth.login');
	// }else {
	// 	return view('client.index');
	// }
});

Route::get('/admin', function () {
    return view('welcome');
});


Route::post('/api/cashless/update/{id}', 'VMController@updateCashlessApi');
Route::delete('/api/cashless/{id}/delete', 'VMController@destroyCashlessApi');
Route::post('/api/cashless/create', 'VMController@createCashlessApi');
Route::patch('/cashless/{id}/update', 'VMController@updateCashless');
Route::get('/cashless/{id}/edit', 'VMController@editCashless');
Route::delete('/cashless/{id}/destroy', 'VMController@destroyCashless');
Route::post('/cashless/store', 'VMController@storeCashless');
Route::get('/cashless/create', 'VMController@getCashlessCreate');
Route::post('/api/cashless/data', 'VMController@getCashlessIndexApi');
Route::get('/cashless', 'VMController@cashlessIndex');

// Route::post('/simcard/update/{id}', 'VMController@updateSimcard');
Route::post('/api/simcard/update/{id}', 'VMController@updateSimcardApi');
Route::delete('/api/simcard/{id}/delete', 'VMController@destroySimcardApi');
Route::post('/api/simcard/create', 'VMController@createSimcardApi');

Route::patch('/simcard/{id}/update', 'VMController@updateSimcard');
Route::get('/simcard/{id}/edit', 'VMController@editSimcard');
Route::delete('/simcard/{id}/destroy', 'VMController@destroySimcard');
Route::post('/simcard/store', 'VMController@storeSimcard');
Route::get('/simcard/create', 'VMController@getSimcardCreate');
Route::post('/api/simcard/data', 'VMController@getSimcardIndexApi');
Route::get('/simcard', 'VMController@simcardIndex');


Route::patch('/vm/{id}/update', 'VMController@updateVending');
Route::get('/vm/{id}/edit', 'VMController@editVending');
Route::delete('/vm/{id}/destroy', 'VMController@destroyVending');
Route::post('/vm/store', 'VMController@storeVending');
Route::get('/vm/create', 'VMController@getVendingCreate');
Route::post('/api/vm/data', 'VMController@getVendingIndexApi');
Route::get('/vm', 'VMController@vendingIndex');

Route::post('/testing', 'VMController@dataIndex')->middleware('apiguard');

Route::get('/vendcomplain', 'ClientController@getVendComplainIndex');
Route::post('/vendcomplain/create', 'ClientController@sendVendingComplain');

Route::post('/api/job/verify', 'JobController@verifyJobApi');
Route::post('/api/job/update', 'JobController@updateJobApi');
Route::delete('/api/job/{id}/delete', 'JobController@destroyJobApi');
Route::post('/api/job/create', 'JobController@createJobApi');
Route::post('/api/jobs', 'JobController@getJobsApi');
Route::get('/jobcard', 'JobController@getJobIndex');

Route::post('/api/personmaintenance/verify', 'PersonController@verifyPersonmaintenanceApi');
Route::post('/api/personmaintenance/update', 'PersonController@updatePersonmaintenanceApi');
Route::delete('/api/personmaintenance/{id}/delete', 'PersonController@destroyPersonmaintenanceApi');
Route::post('/api/personmaintenance/create', 'PersonController@createPersonmaintenanceApi');
Route::post('/api/personmaintenances', 'PersonController@getPersonmaintenancesApi');
Route::get('/personmaintenance', 'PersonController@getPersonmaintenanceIndex');

Route::post('/api/bom/currency/{bomcurrency_id}/rate', 'BomController@updateBomcurrencyRateApi');
Route::get('/api/bom/currencies/all', 'BomController@getBomcurrenciesSelectApi');
Route::post('/api/bom/currency/update', 'BomController@updateBomcurrencyApi');
Route::delete('/api/bom/currency/{id}/delete', 'BomController@destroyBomcurrencyApi');
Route::post('/api/bom/currency/create', 'BomController@createBomcurrencyApi');
Route::post('/api/bom/currencies', 'BomController@getBomcurrenciesApi');

Route::post('/api/bom/replicate/custcat', 'BomController@replicateBomCustcat');
Route::get('/api/tocustcategory/{from_custcategory_id}', 'BomController@getToCustcategoryIdOptions');
Route::get('/api/bom/groups/all', 'BomController@getBomgroupsSelectApi');
Route::post('/api/bom/group/update', 'BomController@updateBomgroupApi');
Route::delete('/api/bom/group/{id}/delete', 'BomController@destroyBomgroupApi');
Route::post('/api/bom/group/create', 'BomController@createBomgroupApi');
Route::post('/api/bom/groups', 'BomController@getBomgroupsApi');

Route::post('/api/bompartconsumable/custcat', 'BomController@bindBompartconsumableCustcat');
Route::delete('/api/bompartconsumable/drawing/{bompartconsumable_id}/delete', 'BomController@removeBompartconsumableDrawingApi');
Route::get('/api/bompartconsumable/{bompartconsumable_id}', 'BomController@getBompartconsumableSingleApi');
Route::post('/bompartconsumable/drawing/upload/{bompartconsumable_id}', 'BomController@uploadBompartconsumableDrawing');
Route::post('/api/bompartconsumable/update', 'BomController@updateBompartconsumable');
Route::post('/api/bompartconsumable/create', 'BomController@createBompartconsumable');
Route::delete('/api/bom/bompartconsumable/{id}/delete', 'BomController@destroyBompartconsumableApi');
Route::post('/api/bompartconsumable/single/qty', 'BomController@updateBompartconsumableQty');
Route::post('/api/bompartconsumable/single/remark', 'BomController@updateBompartconsumableRemark');
Route::get('/api/bompartconsumable_id/increment', 'BomController@getBompartconsumableIncrementApi');
Route::post('/api/bomcomponent/single/qty', 'BomController@updateBomcomponentQty');
Route::post('/api/bomcomponent/single/remark', 'BomController@updateBomcomponentRemark');
Route::post('/api/bomcomponent/custcat', 'BomController@bindBomcomponentCustcat');
Route::post('/api/bomcategory/custcat', 'BomController@bindBomcategoryCustcat');
/*Route::post('/api/bomcategory/{bomcategory_id}/custcategories/remove', 'BomController@removeCustcatBomcategory');
Route::post('/api/bomcategory/{bomcategory_id}/custcategories/add', 'BomController@addCustcatBomcategory');*/
Route::delete('/api/bompart/drawing/{bompart_id}/delete', 'BomController@removeBompartDrawingApi');
Route::get('/api/bompart/{bompart_id}', 'BomController@getBompartSingleApi');
Route::post('/bompart/drawing/upload/{bompart_id}', 'BomController@uploadBompartDrawing');
Route::delete('/api/bomcomponent/drawing/{bomcomponent_id}/delete', 'BomController@removeBomcomponentDrawingApi');
Route::get('/api/bomcomponent/{bomcomponent_id}', 'BomController@getBomcomponentSingleApi');
Route::post('/bomcomponent/drawing/upload/{bomcomponent_id}', 'BomController@uploadBomcomponentDrawing');
Route::delete('/api/bomcategory/drawing/{bomcategory_id}/delete', 'BomController@removeBomcategoryDrawingApi');
Route::get('/api/bomcategory/{bomcategory_id}', 'BomController@getBomcategorySingleApi');
Route::post('/bomcategory/drawing/upload/{bomcategory_id}', 'BomController@uploadBomcategoryDrawing');
Route::post('/api/bomcategory/single/update', 'BomController@updateBomcategoryApi');
Route::post('/api/bomcomponent/single/update', 'BomController@updateBomcomponentApi');
Route::post('/api/bompart_id/validate', 'BomController@validateBompartId');
Route::get('/api/bompart_id/increment', 'BomController@getBompartIncrementApi');
Route::post('/api/bompart/single/update', 'BomController@updateBompartApi');
Route::post('/api/bomcomponent/bompart/create', 'BomController@createBompartByBomcomponent');
Route::post('/api/bompart/single/qty', 'BomController@updateBompartQty');
Route::post('/api/bompart/single/remark', 'BomController@updateBompartRemark');
Route::post('/api/bomtemplate/part/custcat', 'BomController@bindBompartCustcat');
Route::delete('/api/bom/maintenance/{bommaintenance_id}/delete', 'BomController@destroyBommaintenanceApi');
Route::post('/api/bom/maintenances', 'BomController@getBommaintenancesApi');
Route::post('/api/bom/maintenance/submit', 'BomController@createBommaintenanceEntry');
Route::post('/api/bomvending/part/change', 'BomController@changeBomvendingPart');
Route::get('/api/template/component/{component_id}/custcategory/{custcategory_id}', 'BomController@getTemplateByComponentCustcategory');
Route::post('/api/bom/vendings', 'BomController@getVendingsPeopleApi');
Route::post('/api/bom/synctemplate/bomvending', 'BomController@syncTemplateVending');
Route::delete('/api/bom/template/{bomtemplate_id}/delete', 'BomController@destroyTemplateApi');
Route::post('/api/bom/template/custcategory/{custcategory_id}', 'BomController@createBomtemplateApi');
Route::post('/api/bom/templates', 'BomController@getBomtemplateApi');

Route::delete('/api/bom/part/{id}/delete', 'BomController@destroyPartApi');
Route::post('/api/bom/parts', 'BomController@getPartsApi');
Route::post('/api/bom/parts/batchcreate/{component_id}', 'BomController@createPartApi');
Route::get('/api/bom/components/category/{category_id}', 'BomController@getComponentsByCategory');
Route::delete('/api/bom/component/{id}/delete', 'BomController@destroyComponentApi');
Route::post('/api/bom/category/components', 'BomController@getCategoryComponentsApi');
Route::post('/api/bom/component/batchcreate/{category_id}', 'BomController@createComponentApi');
Route::delete('/api/bom/category/{id}/delete', 'BomController@destroyCategoryApi');
Route::post('/api/bom/category/create', 'BomController@createCategoryApi');
Route::post('/api/bom/categories', 'BomController@getCategoriesApi');
Route::get('/bom', 'BomController@index');

Route::get('/shop', 'ShopController@getShopIndex');

Route::post('/api/invsummaries', 'FreportController@getInvoiceSummaryApi');
Route::post('/api/variances', 'FreportController@getVariancesIndex');
Route::post('/api/variances/submitEntry', 'FreportController@submitVarianceEntry');
Route::delete('/api/variances/{id}/delete', 'FreportController@destroyVarianceApi');

Route::post('/api/franchisee/people', 'FreportController@getFranchiseePeopleApi');
Route::match(['get', 'post'], '/franrpt', 'FreportController@getInvoiceBreakdownDetail');


Route::post('/api/franchisee/edit/{ftransaction_id}', 'FtransactionController@editApi');
Route::delete('/api/franchisee/entry/{id}/delete', 'FtransactionController@destroyApi');
Route::post('/api/franchisee/submitEntry', 'FtransactionController@submitEntry');
Route::get('/api/franchisee/auth', 'FtransactionController@getFranchiseeIdApi');
Route::get('/franchisee/download/{id}', 'FtransactionController@generateInvoice');
Route::get('/franchisee/emailInv/{id}', 'FtransactionController@sendEmailInv');
Route::delete('/api/franchise/fdeal/delete/{deal_id}', 'FtransactionController@destroyFdealApi');
Route::post('/franchisee/reverse/{id}', 'FtransactionController@reverse');
Route::delete('/franchisee/{id}', 'FtransactionController@destroy');
Route::patch('/franchisee/{id}', 'FtransactionController@update');
Route::get('/api/franchisee/edit/{id}', 'FtransactionController@editApi');
Route::get('/franchisee/{id}/edit', 'FtransactionController@edit');
Route::get('/franchisee/person/latest/{person_id}', 'FtransactionController@showPersonTransac');
Route::post('/franchisee', 'FtransactionController@store');
Route::get('/franchisee/create', 'FtransactionController@create');
Route::post('/api/franchisee', 'FtransactionController@indexApi');
Route::get('/franchisee', 'FtransactionController@index');

Route::get('/api/zones', 'OperationWorksheetController@getAllZoneApi');
Route::post('/api/detailrpt/operation/zone', 'OperationWorksheetController@updatePersonZone');
Route::post('/api/detailrpt/operation/batchinvoices', 'OperationWorksheetController@generateBatchInvoices');
Route::post('/detailrpt/operation/excel', 'OperationWorksheetController@exportOperationExcel');
Route::post('/api/detailrpt/operation/color', 'OperationWorksheetController@changeOperationWorksheetIndexColor');
Route::post('/api/detailrpt/operation', 'OperationWorksheetController@getOperationWorksheetIndexApi');
Route::post('/api/detailrpt/operation/note/{person_id}', 'OperationWorksheetController@updateOperationNoteApi');
Route::post('/api/operation/day', 'OperationWorksheetController@updateWeekDay');
Route::post('/api/operation/area', 'OperationWorksheetController@updateAreaGroup');
Route::get('/operation/worksheet', 'OperationWorksheetController@getOperationWorksheetIndex');
Route::get('/operation/merchandiser', 'OperationWorksheetController@getMerchandiserIndex');
Route::get('/operation/merchandiser-mobile', 'OperationWorksheetController@getMerchandiserMobileIndex');


Route::get('/api/zones/all', 'ZoneController@getIndexApi');
Route::get('/api/zone/{id}', 'ZoneController@getTruckApi');
Route::get('/zone/data', 'ZoneController@getData');
Route::delete('/zone/data/{id}', 'ZoneController@destroyAjax');
Route::resource('zone', 'ZoneController');


Route::post('/detailrpt/vending/batch/generate', 'VendingController@batchGenerateVendingInvoice');
Route::post('/api/detailrpt/vending/invoice', 'VendingController@getVendingGenerateInvoiceApi');
Route::get('/detailrpt/vending', 'VendingController@getVendingIndex');
Route::delete('/vending/remove/{vending_id}/person/{person_id}', 'VendingController@removeVendingPerson');
Route::post('/vending/add/{person_id}/person', 'VendingController@addVendingPerson');
Route::get('/api/vending/{person_id}/person', 'VendingController@getPersonVendingApi');
Route::get('/api/vending/avail/{person_id}/person', 'VendingController@getPersonAvailableVendingApi');

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

Route::post('/inventory/email', 'InventoryController@invEmailUpdate');
Route::get('/inventory/email', 'InventoryController@invEmail');
Route::post('/inventory/setting', 'InventoryController@invLowest');
Route::get('/inventory/setting', 'InventoryController@invIndex');
Route::get('/inventory/item/{inventory_id}', 'InventoryController@itemInventory');
Route::delete('/inventory/data/{id}', 'InventoryController@destroyAjax');
Route::get('/inventory/data', 'InventoryController@getData');
Route::resource('inventory', 'InventoryController');

Route::get('/happyice', 'ClientController@officialWebsite');
Route::get('/every-morning-healthy', 'ClientController@getEveryMorningHealthyPage');
Route::get('/everymorninghealthy', 'ClientController@everyMorningHealthy');
Route::any('/thankspurchase', 'ClientController@thanksYourPurchase');
Route::get('/brown-sugar-milk-boba-icecream', 'ClientController@brownsugarBobaInquiry');
Route::get('/brown-sugar-milk-boba-party', 'ClientController@brownsugarBobaPartyInquiry');
Route::get('/delivery', 'ClientController@brownsugarBobaInquiry');
Route::get('/icecream-buffet', 'ClientController@icecreamBuffetPage');
Route::get('/menu', 'ClientController@getMenuPage');
Route::get('/every-morning-healthy-order', 'ClientController@everyMorningHealthyOrder');
Route::get('/next-day-delivery', 'ClientController@brownsugarBobaInquiry');
Route::get('/ice-cream-party-package', 'ClientController@iceCreamPartyPackage');
Route::get('/warehouse-sales', 'ClientController@warehouseSales');
Route::get('/terms', 'ClientController@termsAndConditions');
Route::get('/privacy', 'ClientController@privacyPolicy');
Route::get('/franchise', 'ClientController@franchiseIndex');
Route::post('/franchise', 'ClientController@franchiseInquiry');
Route::get('/recruitment', 'ClientController@recruitmentIndex');
Route::get('/vending/funv', 'ClientController@funVendingIndex');
Route::get('/vending/honestv', 'ClientController@honestVendingIndex');
Route::get('/vending/directv', 'ClientController@directVendingIndex');
Route::post('/vending/funv', 'ClientController@funVendingInquiry');
Route::post('/vending/honestv', 'ClientController@honestVendingInquiry');
Route::post('/vending/directv', 'ClientController@directVendingInquiry');
Route::get('/d2d', 'ClientController@d2dIndex');
Route::post('/d2d/email', 'ClientController@emailOrder');
Route::get('/client/item', 'ClientController@clientProduct');
Route::get('/client/register', 'ClientController@getRegister');
Route::get('/client/about', 'ClientController@getAboutUs');
Route::get('/client/product', 'ClientController@getProduct');
Route::get('/client/contact', 'ClientController@getContact');
Route::get('/client/locate', 'ClientController@getLocations');
Route::post('/api/client/locate/{vendingType}', 'ClientController@getPeopleByVendingType');
Route::post('/client/contact', 'ClientController@sendContactEmail');
Route::resource('client', 'ClientController');

Route::get('/position/data', 'PositionController@getData');
Route::delete('/position/data/{id}', 'PositionController@destroyAjax');
Route::resource('position', 'PositionController');

Route::get('/price-template', 'PriceTemplateController@getPriceTemplateIndex');
Route::get('/price-template/{id}/edit', 'PriceTemplateController@editPriceTemplate');
Route::post('/api/price-template/{id}/add-template-item', 'PriceTemplateController@addPriceTemplateItemApi');
Route::post('/api/price-template', 'PriceTemplateController@getPriceTemplatesApi');
Route::post('/api/price-template/replicate', 'PriceTemplateController@replicatePriceTemplateApi');
Route::post('/api/price-template/create', 'PriceTemplateController@createPriceTemplateApi');
Route::post('/api/price-template/store-update', 'PriceTemplateController@storeUpdatePriceTemplateApi');
Route::post('/api/price-template/generate', 'PriceTemplateController@generateTemplateInvoiceApi');
Route::post('/api/price-template/person/bind', 'PriceTemplateController@bindPriceTemplatePersonApi');
Route::post('/api/price-template/person/{id}/unbind', 'PriceTemplateController@unbindPriceTemplatePerson');
Route::post('/api/price-template/attachment', 'PriceTemplateController@uploadAttachment');
Route::delete('/api/price-template/delete/{id}', 'PriceTemplateController@deletePriceTemplateApi');
Route::post('/api/price-template/sort-sequence', 'PriceTemplateController@sortSequenceApi');
Route::post('/api/price-template/renumber-sequence', 'PriceTemplateController@renumberSequenceApi');
Route::post('/api/price-template/{id}/attachment', 'PriceTemplateController@storeAttachmentApi');
Route::post('/api/price-template/attachment/delete', 'PriceTemplateController@deleteAttachmentApi');
Route::get('/price-template/{id}/attachment/{attachmentId}/delete', 'PriceTemplateController@deleteAttachment');
Route::post('/api/price-template-item/{priceTemplateItemId}/item-uom/{itemUomId}/toggle', 'PriceTemplateController@togglePriceTemplateItemUomApi');
Route::get('/api/price-template/price-template-item/{priceTemplateItemId}', 'PriceTemplateController@deletePriceTemplateItemApi');

Route::get('/route-template', 'RouteTemplateController@getRouteTemplateIndex');
Route::post('/api/route-template', 'RouteTemplateController@getRouteTemplatesApi');
Route::post('/api/route-template/store-update', 'RouteTemplateController@storeUpdateRouteTemplateApi');
Route::post('/api/route-template/generate', 'RouteTemplateController@generateTemplateInvoiceApi');
Route::post('/api/route-template/jobassign/create', 'RouteTemplateController@createRouteTemplateFromJobassignApi');
Route::delete('/api/route-template/delete/{id}', 'RouteTemplateController@deleteRouteTemplateApi');

Route::get('/api/people/vend-codes', 'PersonController@getPeopleByVendCode');
Route::get('/api/person/migrate/{id?}', 'PersonController@retrieveCustomerMigration');
Route::get('/api/person/location-type', 'PersonController@syncLocationTypeWithSys');
Route::post('/api/person/edit/{id}', 'PersonController@editApi');
Route::post('/api/person/batch-update', 'PersonController@batchAssignPeople');
Route::get('/api/outletvisit/outcomes', 'PersonController@getOutletVisitOutcomesApi');
Route::post('/api/outletvisits/person/{person_id}', 'PersonController@getOutletVisitsApi');
Route::post('/api/person/outletvisit/{person_id}', 'PersonController@saveOutletVisitPersonApi');
Route::delete('/api/person/outletvisit/{id}', 'PersonController@destroyOutletVisitPersonApi');
Route::get('/api/people/options', 'PersonController@getPeopleOptionsApi');
Route::post('/api/person/file/remove', 'PersonController@removeFileApi');
Route::post('/person/files/update/{person_id}', 'PersonController@updateFilesName');
Route::get('/api/person/files/{person_id}', 'PersonController@getFilesApi');
Route::get('/person/replicate/{person_id}', 'PersonController@replicatePerson');
Route::get('/person/user/{user_id}', 'PersonController@getPersonUserId');
Route::post('/person/{person_id}/note', 'PersonController@storeNote');
Route::get('/person/specific/data/{person_id}', 'PersonController@getPersonData');
Route::get('/person/price/{person_id}', 'PersonController@personPrice');
Route::get('/person/costrate/{person_id}', 'PersonController@getPersonCostRate');
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
Route::get('/api/person/getLatLng/{person_id}', 'PersonController@getDeliveryLatLng');
Route::post('/api/person/storelatlng/{person_id}', 'PersonController@storeDeliveryLatLng');
Route::get('/api/person/persontags/{person_id}', 'PersonController@getPersonTags');
Route::post('/api/person/custtags', 'PersonController@getCustTagsIndexApi');
Route::delete('/api/person/custtag/{id}/destroy', 'PersonController@deleteCustTagApi');
Route::post('/api/person/custtagattach/{id}/unbind', 'PersonController@unbindCustTagAttachment');
Route::get('/person/vend-code/{vendCode}', 'PersonController@getEditByVendCode');
Route::post('/api/persontag/create', 'PersonController@createPersontagApi');
Route::post('/api/persontagattaches/bind', 'PersonController@bindPersontagAttachesApi');
Route::post('/api/person/creation', 'PersonController@getCreationApi');
Route::get('/api/people/last-invoice-date', 'PersonController@getLastInvoiceDateApi');
Route::post('/api/person/location-type', 'PersonController@getLocationTypeApi');
Route::get('/potential', 'PersonController@potentialIndex');
Route::get('/api/vends/{type?}', 'PersonController@getVendsApi');
Route::get('/api/person/{id}/vendcode/{vendCode}', 'PersonController@updatePersonVendCodeApi');
Route::get('/api/person/{id}/detach-vendcode', 'PersonController@detachPersonVendCodeApi');


Route::get('/potential-customer', 'PotentialCustomerController@index');
Route::post('/api/potential-customer', 'PotentialCustomerController@getDataApi');
Route::post('/api/potential-customer/store-update', 'PotentialCustomerController@storeUpdateApi');
Route::post('/api/potential-customer-file', 'PotentialCustomerController@storeAttachment');
Route::post('/api/potential-customer/performance', 'PotentialCustomerController@getPerformanceApi');
Route::post('/api/potential-customer/{id}/attachments', 'PotentialCustomerController@getAttachmentApi');
Route::post('/api/potential-customer-attachment/{id}/delete', 'PotentialCustomerController@deletePotentialCustomerAttachment');
Route::post('/api/potential-customer-attachment/potential-customer/{id}', 'PotentialCustomerController@storePotentialCustomerAttachment');
// Route::post('/api/potential-customer-attachment/{potential-customer-attachment-id}/delete', 'PotentialCustomerController@deletePotentialCustomerAttachment');

Route::post('/api/sales-progress', 'SalesProgressController@getDataApi');


Route::post('/api/meeting-minute', 'MeetingMinuteController@getDataApi');
Route::post('/api/meeting-minute/store-update', 'MeetingMinuteController@storeUpdateApi');

Route::get('/profile/data', 'ProfileController@getData');
Route::delete('/api/profile/{profile_id}/destroy', 'ProfileController@destroyApi');
Route::resource('profile', 'ProfileController');


Route::get('/api/items/unitcosts/profile/{profile_id}', 'ItemController@getUnitcostsWithItemsByProfileApi');
Route::get('/api/items/migrate', 'ItemController@retrieveItems');
Route::post('/api/items', 'ItemController@getItemsApi');
Route::post('/item/active/{item_id}', 'ItemController@setActiveState');
Route::get('/item/qtyorder/{item_id}', 'ItemController@getItemQtyOrder');
Route::post('/api/item/qtyorder/{item_id}', 'ItemController@getItemQtyOrderApi');
Route::post('/api/item/unitcost', 'ItemController@getUnitcostIndexApi');
Route::post('/item/{item_id}/photo', 'ItemController@addImage');
Route::get('/item/image/{item_id}', 'ItemController@imageItem');
Route::delete('/item/image/{item_id}', 'ItemController@destroyImageAjax');
Route::post('/item/image/{item_id}', 'ItemController@editCaption');
Route::get('/item/data', 'ItemController@getData');
Route::delete('/item/data/{id}', 'ItemController@destroyAjax');
Route::get('/api/item/{id}/item-uom', 'ItemController@getItemUomApi');
Route::post('/item/batchupdate/unitcost', 'ItemController@batchUpdateUnitcost');
Route::get('/api/items/options', 'ItemController@getItemsOptionsApi');

Route::post('/api/item/{id}/uom/create-update', 'ItemController@createUpdateItemUomApi');
Route::delete('/api/item/item-uom/{itemUomId}', 'ItemController@deleteItemUomApi');
Route::get('/api/uoms', 'ItemController@getAllUomApi');
Route::resource('item', 'ItemController');

Route::post('/api/item/group', 'ItemGroupController@getItemGroupsIndexApi');
Route::delete('/api/item/group/{id}/destroy', 'ItemGroupController@deleteItemGroupApi');
Route::post('/api/item/group/{id}/unbind', 'ItemGroupController@unbindItemGroupAttachment');
Route::post('/api/item/group/create', 'ItemGroupController@createItemGroupApi');
Route::post('/api/item/group/bind', 'ItemGroupController@bindItemGroupAttachesApi');

Route::get('/api/itemcategories', 'ItemcategoryController@getIndexApi');
Route::get('/api/items/itemcategory/{itemcategory_id}', 'ItemcategoryController@getItemsByItemcategory');
Route::get('/api/itemcategory/{id}', 'ItemcategoryController@getItemcategoryApi');
Route::get('/itemcategory/data', 'ItemcategoryController@getData');
Route::delete('/itemcategory/data/{id}', 'ItemcategoryController@destroyAjax');
Route::resource('itemcategory', 'ItemcategoryController');

Route::get('/api/trucks', 'TruckController@getIndexApi');
Route::get('/api/truck/{id}', 'TruckController@getTruckApi');
Route::get('/truck/data', 'TruckController@getData');
Route::delete('/truck/data/{id}', 'TruckController@destroyAjax');
Route::resource('truck', 'TruckController');

Route::get('/api/cust-prefixes', 'CustPrefixController@getIndexApi');
Route::get('/api/cust-prefixes/{id}', 'CustPrefixController@getCustPrefixApi');
Route::get('/cust-prefixes/data', 'CustPrefixController@getData');
Route::delete('/cust-prefixes/data/{id}', 'CustPrefixController@destroyAjax');
Route::resource('cust-prefixes', 'CustPrefixController');

Route::post('/api/pricematrix/override', 'PriceController@overridePriceMatrixApi');
Route::post('/api/prices/person', 'PriceController@getPersonPricesApi');
Route::post('/pricematrix/batchconfirm', 'PriceController@batchConfirmPriceMatrix');
Route::get('/pricematrix', 'PriceController@getPriceMatrixIndex');
Route::post('/api/pricematrix', 'PriceController@getPriceMatrixIndexApi');
Route::post('/api/pricematrix/edit', 'PriceController@editPriceMatrixApi');
Route::post('/api/pricematrix/costrate/edit', 'PriceController@editCostrateApi');
Route::get('/api/prices/{item_id}/{person_id}', 'PriceController@lookupPrices');
Route::resource('price', 'PriceController');

Route::post('/api/transaction/delete-all-sequences', 'TransactionController@deleteAllSequencesApi');
Route::get('/api/transaction/{id}/person', 'TransactionController@getPersonByTransactionIdApi');
Route::post('/api/transaction/batch/status', 'TransactionController@batchUpdateStatus');
Route::post('/api/transaction/transremark/{id}', 'TransactionController@updateTransremarkById');
Route::get('/api/transaction/excel/histories', 'TransactionController@getLatestImportExcelHistoryNormal');
Route::get('/api/transaction/excel/histories/unit-price', 'TransactionController@getLatestImportExcelHistoryDifferentUnitPrice');
Route::post('/api/transaction/batch/paymentstatus', 'TransactionController@batchUpdatePaymentStatus');
Route::post('/api/transaction/excel/import', 'TransactionController@importExcelTransaction');
Route::post('/api/transaction/excel/import/unit-price', 'TransactionController@importExcelTransactionDifferentUnitPrice');
Route::post('/api/transaction/jobassign/refreshdriver', 'TransactionController@jobAssignRefreshDriver');
Route::post('/api/transaction/initsequence', 'TransactionController@initTransactionsSequence');
Route::post('/api/transaction/sequence/{id}', 'TransactionController@updateTransactionSequence');
Route::post('/api/transaction/batch/jobdriver', 'TransactionController@batchJobAssignDriver');
Route::get('/transaction/jobassign', 'TransactionController@jobAssignIndex');
Route::post('/api/transaction/jobassign', 'TransactionController@getJobAssignData');
Route::post('/api/transaction/jobassign/pdf/{type}', 'TransactionController@getJobAssignPdf');
Route::post('/api/transaction/jobassign/sortdrivertable', 'TransactionController@sortJobAssignDriverTable');
Route::post('/api/transaction/batch/deliverydate', 'TransactionController@batchUpdateDeliveryDate');
Route::post('/api/transaction/batch/deliverydate/jobassign', 'TransactionController@batchUpdateDeliveryDateJobAssign');
Route::post('/api/transaction/batchdriver', 'TransactionController@batchAssignDriver');
Route::post('/api/transaction/is_important/{id}', 'TransactionController@isImportantChanged');
Route::post('/api/transaction/is_service/{id}/{type}', 'TransactionController@isServiceChanged');
Route::post('/api/transaction/driver/quickupdate', 'TransactionController@driverQuickUpdate');
Route::post('/api/transaction/driver/quickupdate/jobassign', 'TransactionController@driverQuickUpdateJobAssign');
Route::post('/api/transaction/storelatlngarr', 'TransactionController@storeDeliveryLatLngArr');
Route::post('/api/transaction/storelatlng/{id}', 'TransactionController@storeDeliveryLatLng');
Route::get('/transaction/revert/confirm/{transaction_id}', 'TransactionController@revertToConfirmStatus');
Route::post('/transaction/export/accconsolidate/pdf', 'TransactionController@exportAccConsolidatePdf');
Route::get('/transaction/email/subscription', 'TransactionController@subscibeTransactionEmail');
Route::get('/api/transaction/email/subscription', 'TransactionController@subscibeTransactionEmailApi');
Route::get('/api/transaction/email/nonsubscription', 'TransactionController@nonSubscibeTransactionEmailApi');
Route::post('/api/transaction/email/addsubscriber', 'TransactionController@addSubscriberTransactionEmailApi');
Route::delete('/api/transaction/email/removesubscriber/{user_id}', 'TransactionController@removeSubscriberTransactionEmailApi');
Route::delete('/transaction/invoice/attach/{atttachment_id}/remove', 'TransactionController@removeAttachment');
Route::post('/transaction/invoice/attach/{transaction_id}', 'TransactionController@addInvoiceAttachment');
Route::get('/api/transaction/edit/{id}', 'TransactionController@editApi');
Route::get('/transaction/freeze/date', 'TransactionController@getFreezeInvoiceDate');
Route::get('/api/transaction/freeze/date', 'TransactionController@getFreezeInvoiceDateApi');
Route::post('/transaction/freeze/date', 'TransactionController@freezeInvoiceDate');
Route::get('/transaction/emailInv/{trans_id}', 'TransactionController@sendEmailInv');
Route::post('/transaction/rpt/{trans_id}', 'TransactionController@rptDetail');
Route::post('/transaction/reverse/{trans_id}', 'TransactionController@reverse');
Route::get('/transaction/person/latest/{person_id}', 'TransactionController@showPersonTransac');
Route::get('/transaction/status/{transaction_id}', 'TransactionController@changeStatus');
Route::post('/transaction/singlestatus/{transaction_id}', 'TransactionController@changeSingleStatus');
Route::post('/transaction/daterange', 'TransactionController@searchDateRange');
Route::get('/transaction/log/{trans_id}', 'TransactionController@generateLogs');
Route::post('/transaction/replicate/{transaction_id}', 'TransactionController@replicateTransaction');
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
Route::post('/transaction/signature/submit/{transaction_id}', 'TransactionController@saveSignature');
Route::get('/transaction/signature/delete/{transaction_id}', 'TransactionController@deleteSignature');
Route::post('/transaction/files/update/{transaction_id}', 'TransactionController@updateFilesName');
Route::get('/transaction/{id}/service/create', 'TransactionController@createService');
Route::post('/transaction/service/store', 'TransactionController@storeService');
Route::post('/api/transaction/{id}/service/store', 'TransactionController@storeServiceApi');
Route::post('/api/transaction/service/{id}/attachment/{type}', 'TransactionController@uploadAttachmentServiceApi');
Route::get('/api/transaction/{id}/services', 'TransactionController@getServicesApi');
Route::post('/api/transaction/service/{serviceId}/delete', 'TransactionController@deleteServiceApi');
Route::post('/api/transaction/service/{serviceId}/cancel', 'TransactionController@cancelServiceApi');
Route::post('/api/transaction/service/{serviceId}/complete', 'TransactionController@completeServiceApi');
Route::post('/api/transaction/service/{serviceId}/attachment/{attachmentId}/delete', 'TransactionController@deleteServiceAttachmentApi');
Route::post('/api/transaction/service/sync', 'TransactionController@syncServiceApi');
Route::post('/api/transaction/service/{serviceId}/status', 'TransactionController@changeServiceStatus');
Route::get('/api/transaction/service/attachment/{id}', 'TransactionController@attachmentId');
Route::get('/api/transaction/{id}/service/completion', 'TransactionController@checkServiceCompletion');
Route::post('/api/transaction/{id}/cancelConfirmation', 'TransactionController@destroy');
Route::post('/api/transaction/{id}/sync-stock-action-deals', 'TransactionController@syncStockActionDeals');
Route::resource('transaction', 'TransactionController');

Route::get('/hdprofile/transation', 'TransactionController@hdprofileIndex');

Route::delete('/api/deal/delete/{deal_id}', 'DealController@destroyAjax');
Route::get('/deal/data/{transaction_id}', 'DealController@getData');
Route::resource('deal', 'DealController');

Route::get('/user/driver/active', 'UserController@getActiveDriverData');
Route::post('/user/activation/{user_id}', 'UserController@userActivationControl');
Route::get('/user/data/{user_id}', 'UserController@getUser');
Route::get('/user/data', 'UserController@getData');
Route::delete('/user/data/{id}', 'UserController@destroyAjax');
Route::resource('user', 'UserController');
Route::get('/user/member/{user_id}/{level}', 'UserController@convertInitD');
Route::get('/api/user/{user_id}/profile', 'UserController@getProfileByUser');
Route::get('/api/user/{user_id}/nonprofile', 'UserController@getNotProfileByUser');
Route::post('/user/{user_id}/addprofile', 'UserController@addProfileByUser');
Route::delete('/user/{user_id}/removeprofile/{profile_id}', 'UserController@removeProfileByUser');
Route::post('/user/{user_id}/addcustcat', 'UserController@addCustcategoryIdByUser');
Route::delete('/user/{user_id}/removecustcat/{custcategoryId}', 'UserController@removeCustcategoryIdByUser');

// Route::get('/api/racking-configs/user/{userId}/{type}', 'RackingConfigController@getCustcategoryByUserIdApi');
Route::post('/racking-configs/create', 'RackingConfigController@createRackingConfigApi');
Route::get('/api/racking-configs/{id}', 'RackingConfigController@getRackingConfigApi');
Route::post('/racking-configs/data', 'RackingConfigController@getData');
Route::delete('/racking-configs/data/{id}', 'RackingConfigController@destroyAjax');
Route::post('/racking-configs/{id}/attachment/create', 'RackingConfigController@createAttachment');
Route::delete('/racking-configs/{id}/attachment/{attachmentId}/delete', 'RackingConfigController@removeAttachment');
Route::post('/api/racking-configs/{id}/attachment/{attachmentId}/delete', 'RackingConfigController@removeAttachmentApi');
Route::resource('racking-configs', 'RackingConfigController');

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

Route::post('/pdf/detailrpt/stock/billing', 'DetailRptController@exportBillingPdf');
Route::match(['get', 'post'], '/detailrpt/stock/date', 'DetailRptController@getStockDate');
Route::get('/detailrpt/stock/billing', 'DetailRptController@getStockBilling');
Route::post('/api/detailrpt/stock/billing', 'DetailRptController@getStockBillingApi');
Route::match(['get', 'post'], '/detailrpt/stock/customer', 'DetailRptController@getStockPerCustomer');
Route::match(['get', 'post'], '/detailrpt/invbreakdown/detail', 'DetailRptController@getInvoiceBreakdownDetail');
Route::get('/detailrpt/invbreakdown/detail/v2', 'DetailRptController@getInvoiceBreakdownDetailv2');
Route::post('/api/detailrpt/invbreakdown/detailv2', 'DetailRptController@getInvoiceBreakdownDetailv2Api');
Route::get('/detailrpt/invbreakdown/summary', 'DetailRptController@getInvoiceBreakdownSummary');
Route::post('/api/detailrpt/invbreakdown/summary', 'DetailRptController@getInvoiceBreakdownSummaryApi');
Route::get('/detailrpt/account', 'DetailRptController@accountIndex');
Route::post('/api/detailrpt/account/custdetail', 'DetailRptController@getAccountCustdetailApi');
Route::post('/api/detailrpt/account/outstanding', 'DetailRptController@getAccountOutstandingApi');
Route::post('/api/detailrpt/account/paydetail', 'DetailRptController@getAccountPaydetailApi');
Route::post('/api/detailrpt/account/paysummary/verify', 'DetailRptController@verifyAccountPaysummaryApi');
Route::post('/api/detailrpt/account/paysummary', 'DetailRptController@getAccountPaysummaryApi');
Route::post('/detailrpt/account/paysummary', 'DetailRptController@submitPaySummary');
Route::get('/detailrpt/sales', 'DetailRptController@salesIndex');
Route::post('/api/detailrpt/sales/monthly-report', 'DetailRptController@getSalesMonthlyReportApi');
Route::post('/api/detailrpt/sales/custdetail', 'DetailRptController@getSalesCustdetailApi');
Route::post('/api/detailrpt/sales/custsummary', 'DetailRptController@getSalesCustSummaryApi');
Route::post('/api/detailrpt/sales/custsummary-group', 'DetailRptController@getSalesCustSummaryGroupApi');
Route::post('/api/detailrpt/sales/productday', 'DetailRptController@getSalesProductDetailDayApi');
Route::post('/api/detailrpt/sales/productmonth', 'DetailRptController@getSalesProductDetailMonthApi');
Route::get('/detailrpt/sales/{item_id}/thismonth', 'DetailRptController@getProductDetailMonthThisMonth');
Route::post('/api/detailrpt/sales/{item_id}/thismonth', 'DetailRptController@getProductDetailMonthThisMonthApi');
Route::post('/detailrpt/account/custdetail/batchpdf', 'DetailRptController@batchDownloadPdf');

Route::post('/api/personassets', 'PersonassetController@indexApi');
Route::get('/personasset', 'PersonassetController@index');
Route::post('/api/personasset/create', 'PersonassetController@createApi');
Route::post('/api/personasset/update', 'PersonassetController@updateApi');
Route::delete('/api/personasset/{id}/delete', 'PersonassetController@destroyApi');
Route::post('/api/personassetmovements', 'PersonassetController@indexMovementApi');
Route::post('/api/personassetcurrents', 'PersonassetController@indexCurrentApi');

Route::post('/api/transactionpersonasset/create', 'TransactionpersonassetController@createApi');
Route::get('/api/transactionpersonasset/index/{transaction_id}', 'TransactionpersonassetController@indexApi');
Route::post('/api/transactionpersonasset/update', 'TransactionpersonassetController@updateApi');
Route::delete('/api/transactionpersonasset/{id}/delete', 'TransactionpersonassetController@destroyApi');

Route::post('/api/dailyreport/account-manager-performance', 'DailyreportController@getAccountManagerPerformanceApi');
Route::get('/dailyreport/account-manager-performance', 'DailyreportController@getAccountManagerPerformanceIndex');
Route::get('/dailyreport/commission', 'DailyreportController@commissionIndex');
Route::post('/api/dailyreport/index', 'DailyreportController@indexApi');
Route::post('/api/dailyreport/location-count', 'DailyreportController@getLocationCountApi');
Route::get('/dailyreport/driver-location-count', 'DailyreportController@driverNumberOfLocationIndex');
Route::post('/api/dailyreport/driver-location-count/update/{status}', 'DailyreportController@updateLocationCountApi');

Route::get('/performance/office', 'PerformanceController@officeIndex');
Route::post('/api/performance/office', 'PerformanceController@getOfficeIndexApi');
Route::get('/performance/office/create', 'PerformanceController@createTask');
Route::post('performance/office/store', 'PerformanceController@storeTask');

Route::get('/freezer/data', 'FreezerController@getData');
Route::delete('/freezer/data/{id}', 'FreezerController@destroyAjax');
Route::resource('freezer', 'FreezerController');

Route::get('/accessory/data', 'AccessoryController@getData');
Route::delete('/accessory/data/{id}', 'AccessoryController@destroyAjax');
Route::resource('accessory', 'AccessoryController');

Route::get('/payterm/data', 'PaytermController@getData');
Route::delete('/payterm/data/{id}', 'PaytermController@destroyAjax');
Route::resource('payterm', 'PaytermController');

Route::post('/api/custcat/group', 'CustcategoryGroupController@getCustcategoryGroupsIndexApi');
Route::delete('/api/custcat/group/{id}/destroy', 'CustcategoryGroupController@deleteCustcategoryGroupApi');
Route::post('/api/custcat/group/{id}/unbind', 'CustcategoryGroupController@unbindCustcategoryGroupAttachment');
Route::post('/api/custcat/group/create', 'CustcategoryGroupController@createCustcategoryGroupApi');
Route::post('/api/custcat/group/bind', 'CustcategoryGroupController@bindCustcategoryGroupAttachesApi');

Route::get('/api/custcat/user/{userId}/{type}', 'CustcategoryController@getCustcategoryByUserIdApi');
Route::get('/api/custcat/{id}', 'CustcategoryController@getCustcategoryApi');
Route::post('/custcat/data', 'CustcategoryController@getData');
Route::delete('/custcat/data/{id}', 'CustcategoryController@destroyAjax');
Route::post('/custcat/{id}/attachment/create', 'CustcategoryController@createAttachment');
Route::delete('/custcat/{id}/attachment/{attachmentId}/delete', 'CustcategoryController@removeAttachment');
Route::post('/api/custcat/{id}/attachment/{attachmentId}/delete', 'CustcategoryController@removeAttachmentApi');
// Route::post('/api/custcat/{id}/attachment/{attachmentId}/')
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