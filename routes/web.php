<?php
use App\Http\Middleware\CheckLogin;
Auth::routes();
Route::group(['middleware' => [CheckLogin::class]], function () {
Route::get('/', 'MainController@index');
});
Route::get('/dashboard','MainController@dashboard');
Route::get('/sj/dashboard','MainController@sj_dashboard');
Route::get('upload/sj/dashboard','MainController@upload_sj_dashboard');
Route::post('upload/sj/dashboard','MainController@upload_sj_dashboard_store');
Route::get('/sj_balik','MainController@sj_balik');
Route::post('/sj_balik','MainController@sj_balik_store');
Route::post('update_sj_balik_ppic_upload','MainController@update_sj_balik_ppic_upload');
Route::get('/terima_finance','MainController@terima_finance');
Route::post('/terima_finance','MainController@terima_finance_store');
Route::post('/update_fin_upload','MainController@update_fin_upload');
Route::get('/delete_sj/{id}','MainController@del_ppic');
Route::post('/filter_view','MainController@filter_view');
Route::get('/sj_outstanding','MainController@sj_outstanding');
Route::get('/sj_outstanding_finance','MainController@sj_outstanding_finance');
Route::post('/data_sj','MainController@data_sj');
Route::post('/data_outstanding_sj','MainController@data_outstanding_sj');
Route::post('/data_outstanding_sj_7_day','MainController@data_outstanding_sj_7_day');
Route::post('/data_outstanding_sj_7_day_finance','MainController@data_outstanding_sj_7_day_finance');
Route::get('/edit_sj/{id}','MainController@sj_update');
Route::post('/edit_sj/{id}','MainController@sj_update_store');
Route::get('/create/sj','MainController@create_sj');
Route::post('/create/sj','MainController@create_sj_store');
Route::get('/download_sj','MainController@download_sj');


Route::post('/customer','MainController@customer_store')->name('customer.store');
Route::get('/customer','MainController@customer')->name('customer.index');
Route::get('/customer/create','MainController@customer_create')->name('customer.create');
Route::post('/customer/store-form','MainController@customer_store_form')->name('customer.store.form');
Route::get('/edit_customer/{id}','MainController@customer_edit')->name('customer.edit');
Route::post('/edit_customer/{id}','MainController@customer_update')->name('customer.update');
Route::get('/delete_customer/{id}','MainController@customer_delete')->name('customer.delete');

Route::post('/invoice','MainController@update_invoice_upload');
Route::get('/invoice','MainController@invoice');
Route::get('/invoicing','MainController@invoicing');
Route::post('/data_invoice','MainController@data_invoice');

Route::get('/sj_error','MainController@sj_error');
Route::post('/data_sj_error','MainController@data_sj_error');
