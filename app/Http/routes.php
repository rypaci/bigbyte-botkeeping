<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::auth();

/* add a <body> class in each view */
View::composer('*', function($view) {
    $view
        ->with('view_name', str_replace('.', '_', $view->getName()))
        ->with('route_uri', \Request::route()->uri());
    // dd( \Request::segments() );
});

Route::group(['middlewareGroups'=>['web', 'auth']], function() {
    Route::post('adjusting/add-form-validate', 'AdjustmentController@customtFormValidation');
    Route::patch('adjusting/edit-form-validate', 'AdjustmentController@customtFormValidation');
    // Route::resource('adjustment', 'AdjustmentController');
    Route::resource('adjusting', 'AdjustmentController');

    Route::get('billing-invoice/get-service', 'BillingInvoiceController@getService');
    Route::get('billing-invoice/get-services', 'BillingInvoiceController@getServices');
    Route::get('billing-invoice/account-details', 'BillingInvoiceController@accountDetails');
    Route::patch('billing-invoice/account-details', 'BillingInvoiceController@accountDetailsUpdate');
    Route::post('billing-invoice/add-form-validate', 'BillingInvoiceController@customtFormValidation');
    Route::patch('billing-invoice/edit-form-validate', 'BillingInvoiceController@customtFormValidation');
    Route::resource('billing-invoice', 'BillingInvoiceController');

    Route::get('cash-invoice/find-withholdingtax',  'CashInvoiceController@findWithHoldingTax');
    Route::get('cash-invoice/find-customer-only', 'CashInvoiceController@findCustomerOnly');
    Route::get('cash-invoice/find-chart-level', 'CashInvoiceController@findChartAccountLevel');
    Route::get('cash-invoice/find-discount', 'CashInvoiceController@findSCPWDDiscount');
    Route::get('cash-invoice/find-vat', 'CashInvoiceController@findVat');
    Route::get('cash-invoice/find-product', 'CashInvoiceController@findProduct');
    Route::get('cash-invoice/find-product-autosuggest', 'CashInvoiceController@findProductAutoSuggest');
    Route::get('cash-invoice/find-customer', 'CashInvoiceController@getCustomers');
    Route::get('cash-invoice/account-details', 'CashInvoiceController@accountDetails');
    Route::patch('cash-invoice/account-details', 'CashInvoiceController@accountDetailsUpdate');
    Route::post('cash-invoice/add-form-validate', 'CashInvoiceController@customtFormValidation');
    Route::patch('cash-invoice/edit-form-validate', 'CashInvoiceController@customtFormValidation');
    Route::resource('cash-invoice','CashInvoiceController');

    Route::get('cash-payment-voucher/get-coa', 'CashPaymentVoucherController@getCoa');
    Route::get('cash-payment-voucher/get-cv-number', 'CashPaymentVoucherController@getCvNumber');
    Route::get('cash-payment-voucher/account-details','CashPaymentVoucherController@accountDetails');
    Route::patch('cash-payment-voucher/account-details','CashPaymentVoucherController@accountDetailsUpdate');
    Route::post('cash-payment-voucher/add-form-validate', 'CashPaymentVoucherController@customtFormValidation');
    Route::patch('cash-payment-voucher/edit-form-validate', 'CashPaymentVoucherController@customtFormValidation');
    Route::resource('cash-payment-voucher', 'CashPaymentVoucherController');

    Route::get('chart-account/code-and-sub-accounts', 'ChartAccountController@getCodeAndSubAccounts');
    Route::get('chart-account/code', 'ChartAccountController@getCode');
    Route::get('voucher/get-date-range', 'ChartAccountController@getSales');
    Route::resource('chart-account', 'ChartAccountController');

    Route::resource('chart-account-type', 'ChartAccountTypeController');

    Route::get('collection-receipt/account-details', 'CollectionReceiptController@accountDetails');
    Route::patch('collection-receipt/account-details','CollectionReceiptController@accountDetailsUpdate');
    Route::post('collection-receipt/add-form-validate', 'CollectionReceiptController@customtFormValidation');
    Route::patch('collection-receipt/edit-form-validate', 'CollectionReceiptController@customtFormValidation');
    Route::resource('collection-receipt', 'CollectionReceiptController');

    // Route::resource('company', 'CompanyController');
    Route::get('company', 'CompanyController@index');
    Route::get('company/info', 'CompanyController@getInfo');
    Route::patch('company/info', 'CompanyController@saveInfo');

    Route::get('credit-invoice/account-details', 'CreditInvoiceController@accountDetails');
    Route::patch('credit-invoice/account-details', 'CreditInvoiceController@accountDetailsUpdate');
    Route::post('credit-invoice/add-form-validate', 'CreditInvoiceController@customtFormValidation');
    Route::patch('credit-invoice/edit-form-validate', 'CreditInvoiceController@customtFormValidation');
    Route::resource('credit-invoice','CreditInvoiceController');

    Route::get('customer/get-billing-invoices', 'CustomerCRUDController@getBillingInvoices');
    Route::get('customer/get-credit-invoices', 'CustomerCRUDController@getCreditInvoices');
    Route::get('customer/get-open-invoices', 'CustomerCRUDController@getOpenInvoices');
    Route::resource('customer','CustomerCRUDController');

    Route::resource('discount', 'DiscountController');
    // Route::get('service-list', function () { return view('home'); });
    Route::get('employee', function () { return view('home'); });
    //Route::get('list-employees', function () { return view('home'); });
    //Route::get('customer', function () { return view('home'); });

    Route::get('journal','JournalController@index');

    Route::get('ledger', 'LedgerController@index');
    Route::get('ledger/get-customers', 'LedgerController@getCustomers');
    Route::get('ledger/get-vendors', 'LedgerController@getVendors');

    Route::get('list-employees/destroy/{id}', ['as' => 'list-employees.get.destroy', 'uses' => 'EmployeeCRUDController@getDestroy']);
    Route::post('list-employees/payroll', 'EmployeeCRUDController@addToPayroll');
    Route::resource('list-employees', 'EmployeeCRUDController');

    Route::get('official-receipt/account-details', 'OfficialReceiptController@accountDetails');
    Route::patch('official-receipt/account-details','OfficialReceiptController@accountDetailsUpdate');
    Route::post('official-receipt/add-form-validate', 'OfficialReceiptController@customtFormValidation');
    Route::patch('official-receipt/edit-form-validate', 'OfficialReceiptController@customtFormValidation');
    Route::resource('official-receipt', 'OfficialReceiptController');

    Route::get('open-invoice/account-details', 'OpenInvoiceController@accountDetails');
    Route::patch('open-invoice/account-details','OpenInvoiceController@accountDetailsUpdate');
    Route::post('open-invoice/add-form-validate', 'OpenInvoiceController@customtFormValidation');
    Route::patch('open-invoice/edit-form-validate', 'OpenInvoiceController@customtFormValidation');
    Route::resource('open-invoice', 'OpenInvoiceController');

    // Route to update daily and monthly payroll
    Route::get('payroll/edit-daily-payroll/{id}', 'PayrollController@editDailyPayroll');
    Route::get('payroll/edit-monthly-payroll/{id}', 'PayrollController@editMonthlyPayroll');
    Route::get('payroll/update-payroll', 'PayrollController@updatePayroll');

    // Route to save daily and monthly payroll
    Route::get('payroll/create-daily-payroll', 'PayrollController@createDailyPayroll');
    Route::get('payroll/create-monthly-payroll', 'PayrollController@createMonthlyPayroll');
    Route::get('payroll/save-payroll', 'PayrollController@savePayroll');

    Route::get('payroll/destroy/{pay_id}', ['as' => 'payroll.get.destroy', 'uses' => 'PayrollController@getPayrollDestroy']);
    Route::get('payroll/payroll-details', 'PayrollController@payrollDetails');
    Route::post('payroll/payroll-details', 'PayrollController@payrollDetailsByName');
    Route::resource('payroll','PayrollController');
    
    Route::get('cash-advance/details/date-result', 'CashAdvanceController@dateResults');
    Route::get('cash-advance/results', 'CashAdvanceController@searchResults');
    Route::get('cash-advance/details/{id}', 'CashAdvanceController@cashadvanceDetails');
    Route::resource('cash-advance','CashAdvanceController');

    Route::resource('product-list', 'ItemCRUDController');
    
    Route::post('product/save-image', 'ProductController@saveImage');
    Route::get('product/add-image/{id}', 'ProductController@addImage');
    Route::post('product/update-image', 'ProductController@updateImage');
    Route::get('product/edit-image/{id}', 'ProductController@editImage');
    Route::get('product/image-delete/{id}', 'ProductController@imageDelete');
    Route::get('product/search-product', 'ProductController@searchProduct');
    Route::get('product/details/detailsdestroy/{id}', ['as' => 'product.get.detailsdestroy', 'uses' => 'ProductController@detailsDestroy']);
    Route::get('product/details/add-stock/{id}', 'ProductController@addStock');
    Route::get('product/details/save-stock', 'ProductController@saveStock');
    Route::get('product/details/{id}', 'ProductController@productDetails');
    Route::post('product/details', 'ProductController@updateQty');
    Route::resource('product', 'ProductController');

    Route::get('profile', 'UserController@getProfile');
    Route::post('profile', 'UserController@postProfile');

    // Route::get('report/download', 'ReportController@download');
    Route::get('report', 'ReportController@index2');
    Route::get('report-old', 'ReportController@index');

    Route::resource('service-list', 'ServiceCRUDController');

    Route::resource('sub-account-type', 'SubAccountTypeController');

    Route::get('supplier-invoice/find-vendor', 'SupplierInvoiceController@findVendor');
    Route::get('supplier-invoice/find-vendors', 'SupplierInvoiceController@findVendors');
    Route::get('supplier-invoice/get-account-details', 'SupplierInvoiceController@getAccountDetails');
    Route::get('supplier-invoice/get-accounts-taxes', 'SupplierInvoiceController@getAccountsTaxes');
    Route::get('supplier-invoice/get-taxes', 'SupplierInvoiceController@getTaxes');
    Route::get('supplier-invoice/account-details', 'SupplierInvoiceController@accountDetails');
    Route::patch('supplier-invoice/account-details', 'SupplierInvoiceController@accountDetailsUpdate');
    Route::post('supplier-invoice/add-form-validate', 'SupplierInvoiceController@addFormValidation');
    Route::patch('supplier-invoice/add-form-validate', 'SupplierInvoiceController@addFormValidation');
    Route::resource('supplier-invoice', 'SupplierInvoiceController');

    Route::resource('tax', 'TaxController');

    Route::get('user/access-token', 'UserController@getAccessToken');
    Route::resource('user', 'UserController');

    Route::get('vendors/get-supplier-invoices', 'VendorController@getSupplierInvoices');
    Route::resource('vendors', 'VendorController');
    
    // New additional routes
    Route::get('water-refilling-monitoring/bulk-pay', 'WaterRefillingController@bulkPay');
    Route::get('water-refilling-monitoring/find-product-autosuggest', 'WaterRefillingController@findProductAutoSuggest');
    Route::get('water-refilling-monitoring/find-product', 'WaterRefillingController@findProduct');
    Route::delete('water-refilling-monitoring/truncate', array('as'=>'water-refilling-monitoring.truncate', 'uses'=>'WaterRefillingController@truncate'));
    Route::delete('water-refilling-monitoring/reset', array('as'=>'water-refilling-monitoring.reset', 'uses'=>'WaterRefillingController@reset'));
    Route::delete('water-refilling-monitoring/resetalk', array('as'=>'water-refilling-monitoring.resetalk', 'uses'=>'WaterRefillingController@resetalkaline'));
    Route::delete('water-refilling-monitoring/resetmin', array('as'=>'water-refilling-monitoring.resetmin', 'uses'=>'WaterRefillingController@resetmineral'));
    Route::get('water-refilling-monitoring/reports/paid/{id}', ['as' => 'water-refilling-monitoring/reports.get.paid', 'uses' => 'WaterRefillingController@paid']);
    Route::get('water-refilling-monitoring/find-customer-only', 'WaterRefillingController@findCustomerOnly'); // Ajax purpose only
    Route::get('water-refilling-monitoring/reports/destroy/{id}', ['as' => 'water-refilling-monitoring/reports.get.destroy', 'uses' => 'WaterRefillingController@getRecordDestroy']);
    Route::get('water-refilling-monitoring/reports', 'WaterRefillingController@reports');
    Route::get('water-refilling-monitoring/search-date-range', 'WaterRefillingController@searchDateRange');
    Route::get('water-refilling-monitoring/wrm-charts', 'WaterRefillingController@wrmCharts');
    Route::resource('water-refilling-monitoring', 'WaterRefillingController');

    Route::get('wrmexpenses/find-vendor-only', 'WRMExpensesController@findVendorOnly'); // Ajax purpose only
    Route::get('wrmexpenses/tracks-expenses/destroy/{id}', ['as' => 'wrmexpenses/tracks-expenses.get.destroy', 'uses' => 'WRMExpensesController@getRecordDestroy']);
    Route::get('wrmexpenses/tracks-expenses', 'WRMExpensesController@trackExpenses');
    Route::resource('wrmexpenses', 'WRMExpensesController');

    Route::resource('wrm-regenerate-settings', 'WRMRegenerateSettingsController');

    Route::get('wrm-original-bottles/track-original-bottles/destroy/{id}', ['as' => 'wrm-original-bottles/track-original-bottles.get.destroy', 'uses' => 'WRMOriginalBottlesController@getRecordDestroy']);
    Route::get('wrm-original-bottles/track-original-bottles', 'WRMOriginalBottlesController@reportDetails');
    Route::post('wrm-original-bottles/track-original-bottles', 'WRMOriginalBottlesController@dateByRange');
    Route::resource('wrm-original-bottles', 'WRMOriginalBottlesController');

    Route::get('wrm-damage-bottles/track-damage-bottles/destroy/{id}', ['as' => 'wrm-damage-bottles/track-damage-bottles.get.destroy', 'uses' => 'WRMDamageBottlesController@getRecordDestroy']);
    Route::get('wrm-damage-bottles/track-damage-bottles', 'WRMDamageBottlesController@reportDetails');
    Route::post('wrm-damage-bottles/track-damage-bottles', 'WRMDamageBottlesController@dateByRange');
    Route::resource('wrm-damage-bottles', 'WRMDamageBottlesController');

    Route::resource('wrm-change-sap-filter', 'WRMChangeSAPfilterController');

    Route::resource('wrm-change-sap-alkaline-filter', 'WRMChangeSAPfilterAlkalineController');

    Route::resource('wrm-change-sap-mineral-filter', 'WRMChangeSAPfilterMineralController');
    
    Route::get('wrm-issued-bottles/search-result', 'WRMissuedBottlesController@searchResults');
    Route::get('wrm-issued-bottles/issued-bots-details/{id}', 'WRMissuedBottlesController@issuedBottlesDetails');
    Route::resource('wrm-issued-bottles', 'WRMissuedBottlesController');
    
    Route::get('inventory/inventory-date-range', 'InventoryController@inventoryDateRange');
    Route::get('inventory/inventory-details', 'InventoryController@dateRange');
    Route::get('inventory/inventory-details/{id}', 'InventoryController@inventoryDetails');
    Route::get('inventory/search-products', 'InventoryController@searchFilter');
    Route::post('inventory/save-srpriority', 'InventoryController@srPriority');
    Route::resource('inventory', 'InventoryController');
    
    Route::get('daily-time-record/generate-qr-code', 'DTRController@generateQrCode');
    Route::get('daily-time-record/qr-code', 'DTRController@qrCode');
    Route::get('daily-time-record/lunch-break-details/{id}', 'DTRController@lunchBreak');
    Route::get('daily-time-record/finish-break/{id}', 'DTRController@finishBreak');
    Route::get('daily-time-record/search-date-absent', 'DTRController@searchDateAbsent');
    Route::get('daily-time-record/delete-absent/{id}', 'DTRController@deleleteAbsent');
    Route::get('daily-time-record/update-absent', 'DTRController@updateAbsent');
    Route::get('daily-time-record/edit-absent/{id}', 'DTRController@editAbsent');
    Route::get('daily-time-record/absent-list/{id}', 'DTRController@absentList');
    Route::get('daily-time-record/save-absent', 'DTRController@saveAbsent');
    Route::get('daily-time-record/absent', 'DTRController@absent');
    Route::get('daily-time-record/save-hours-shifting', 'DTRController@saveHoursShifting');
    Route::get('daily-time-record/hours-shifting/{id}', 'DTRController@hoursShifting');
    Route::get('daily-time-record/save-time-adjustment', 'DTRController@saveTimeAdjustment');
    Route::get('daily-time-record/time-adjustment/{id}', 'DTRController@timeAdjustment');
    Route::get('daily-time-record/send-password/{id}', 'DTRController@sendPassword');
    Route::get('daily-time-record/save-reset-password', 'DTRController@saveResetPassword');
    Route::get('daily-time-record/reset-password/{id}', 'DTRController@resetPassword');
    Route::get('daily-time-record/search-date-dtr-details', 'DTRController@searchDateDtrDetails');
    Route::get('daily-time-record/delete-dtr-details/{id}', 'DTRController@deleteDtrDetails');
    Route::get('daily-time-record/dtr-details/{id}', 'DTRController@dtrDetails');
    Route::post('daily-time-record/dtr-account-search', 'DTRController@dtrAccountSearch');
    Route::get('daily-time-record/dtr-account-list', 'DTRController@dtrAccountList');
    Route::get('daily-time-record/update-password', 'DTRController@updatePassword');
    Route::get('daily-time-record/edit-password', 'DTRController@editPassword');
    Route::get('daily-time-record/save-password', 'DTRController@savePassword');
    Route::get('daily-time-record/create-password', 'DTRController@createPassword');
    Route::get('daily-time-record/logout/{id}', 'DTRController@logOut');
    Route::resource('daily-time-record', 'DTRController');
    
    Route::get('point-of-sale/reports/bulk-pay', 'POSController@bulkPay');
    Route::get('point-of-sale/sales-reports/product-detail-sales/{id}/{datefrom}/{dateto}', 'POSController@productSalesDetails');
    Route::get('point-of-sale/sales-reports/search', 'POSController@searchReports');
    Route::get('point-of-sale/sales-reports', 'POSController@salesReports');
    Route::get('point-of-sale/search-date-range', 'POSController@searchDateRange');
    Route::get('point-of-sale/pay-pos/{id}', 'POSController@paid');
    Route::get('point-of-sale/inventory/inventory-details/{id}', 'POSController@inventoryDetails');
    Route::post('point-of-sale/save-srpriority', 'POSController@saveSRpriority');
    Route::get('point-of-sale/search-products', 'POSController@searchFilter');
    Route::get('point-of-sale/inventory-daterange', 'POSController@inventoryDateRange');
    Route::get('point-of-sale/inventory', 'POSController@inventory');
    Route::get('point-of-sale/delete-pos/{id}', 'POSController@deletePos');
    Route::get('point-of-sale/reports', 'POSController@reports');
    Route::get('point-of-sale/find-product-autosuggest', 'POSController@findProductAutoSuggest'); // Ajax purpose only
    Route::get('point-of-sale/find-product', 'POSController@findProduct'); // Ajax purpose only
    Route::get('point-of-sale/find-customer-only', 'POSController@findCustomerOnly'); // Ajax purpose only
    Route::resource('point-of-sale', 'POSController');

    Route::get('pos-expenses/find-vendors', 'POSExpensesController@findVendors'); // Ajax purpose only
    Route::get('pos-expenses/find-vendor-only', 'POSExpensesController@findVendorOnly'); // Ajax purpose only
    Route::get('pos-expenses/tracks-expenses/{id}', 'POSExpensesController@getRecordDestroy');
    Route::get('pos-expenses/tracks-expenses', 'POSExpensesController@trackExpenses');
    Route::resource('pos-expenses', 'POSExpensesController');

});

Route::get('/clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
 
    return "Cleared!";
});

Route::get('/', function () {
    if (Auth::guest())
        return view('welcome');
    else
        return view('home');
});

// Public WRM Entry
Route::get('/wrm-find-customer-autosuggest', 'WaterRefillingController@publicGetCustomersAutoSuggest'); // Ajax purposes
Route::get('/wrm-find-customer', 'WaterRefillingController@publicFindCustomerOnly'); // Ajax purposes
Route::get('/wrm-product-autosuggest', 'WaterRefillingController@publicFindProductAutoSuggest'); // Ajax purposes
Route::get('/wrm-find-product', 'WaterRefillingController@publicFindProduct'); // Ajax purposes
Route::post('/wrm-save-entry', 'WaterRefillingController@savePOSentry');
Route::get('/water-refilling-monitoring/public-wrm-entry/access', 'WaterRefillingController@posEntryAccess');
Route::post('/wrm-entry-access-verified', 'WaterRefillingController@posEntryAccessVerified');
Route::get('/water-refilling-monitoring/public-wrm-entry/sales-entry', 'WaterRefillingController@publicPOSentry');
Route::get('/wrm-assign-shifting', 'WaterRefillingController@assignShifting');

/*This is public time in and time out*/
Route::post('/view-dtr-history', 'DTRController@viewDtrHistory');
Route::get('/dtr-verification', 'DTRController@dtrVerification');
Route::post('/save-time-in-or-out', 'DTRController@saveTimeInTimeOut');
Route::get('/time-in-time-out', 'DTRController@timeInTimeOut');
Route::post('/generate-employee-qrcode', 'DTRController@generateEmpQrCode');
Route::get('/access-verification', 'DTRController@accessVerification');

// Public POS Entry
Route::get('/find-customer-autosuggest', 'POSController@publicGetCustomersAutoSuggest'); // Ajax purposes
Route::get('/pos-find-customer', 'POSController@publicFindCustomerOnly'); // Ajax purposes
Route::get('/pos-product-autosuggest', 'POSController@publicFindProductAutoSuggest'); // Ajax purposes
Route::get('/pos-find-product', 'POSController@publicFindProduct'); // Ajax purposes
Route::post('/save-pos-entry', 'POSController@savePOSentry');
Route::get('/point-of-sale/pos-entry/access', 'POSController@posEntryAccess');
Route::post('/pos-entry-access-verified', 'POSController@posEntryAccessVerified');
Route::get('/point-of-sale/pos-entry/sales-entry', 'POSController@publicPOSentry');
Route::get('/assign-shifting', 'POSController@assignShifting');

Route::auth();


/* 

composer create-project laravel/laravel quicktax "5.2.*"
php artisan make:auth
update .env for db connection 
php artisan migrate
add views/_includes
add views/layouts/adminlte.blade.php
fix non-object errors in views/_includes/header and sidebar

 */