<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*************************************************
// Admin URL
**************************************************/

Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";

    $cleardebugbar = Artisan::call('debugbar:clear');
    echo "Debug Bar cleared<br>";
});

Route::get('/logout', function() {
 Session::forget('user');
  if(!Session::has('user'))
   {
     return redirect('/');
   }
 });

Route::get('/public', 'HomeController@index');
/***************User****************************/
Route::get('/login', 'AuthController@index');
Route::get('/register', 'AuthController@register');
Route::get('/forgot-password', 'AuthController@forgot_password');
Route::get('/getSelectionData', 'AuthController@getSelectionData');
Route::get('/getSelectionCategoryByCompany', 'AuthController@getSelectionCategoryByCompany');
Route::get('/getSelectionUserByRegion', 'AuthController@getSelectionUserByRegion');
Route::get('/checkEmail', 'AuthController@checkEmail');
Route::get('/reset_password/{code}', 'AuthController@reset_password');
Route::post('/register_data', 'AuthController@register_data');
Route::post('/login_data', 'AuthController@login_data');
Route::post('/submit_forgot_data', 'AuthController@submit_forgot_data');
Route::post('/submit_reset_password', 'AuthController@submit_reset_password');
Route::post('/update_profile', 'AuthController@update_profile');
Route::post('/update_user', 'AuthController@update_user');
Route::post('/submit_change_profile', 'AuthController@submit_change_profile');
Route::post('/user_list', 'AuthController@user_list');
Route::get('/profile', 'AuthController@profile');
Route::get('/change_password', 'AuthController@changePassword');
Route::get('/user-management', 'AuthController@user_management');
Route::post('/deleteData', 'AuthController@deleteData');
Route::get('/edit_user/{id}', 'AuthController@edit_user');
Route::get('/create_user', 'AuthController@create_user');
Route::post('/submit_user', 'AuthController@submit_user');
Route::post('/set_company_id', 'AuthController@set_company_id');
Route::get('/search_employee', 'AuthController@search_employee'); 

/**************Permission***************/
Route::get('/allow_permission/{permission_name}', 'AuthController@allow_permission');
Route::get('/permission', 'AuthController@permission');
Route::get('/create_permission', 'AuthController@create_permission');
Route::get('/edit_permission/{id}', 'AuthController@edit_permission');
Route::get('/update_permissions/{id}', 'AuthController@update_permissions');
Route::post('/get_permission_list', 'AuthController@get_permission_list');
Route::post('/submit_permission', 'AuthController@submit_permission');
Route::post('/delete_permission', 'AuthController@delete_permission');
Route::post('/update_permission_data', 'AuthController@update_permission_data');

/**************roles***************/
Route::get('/roles', 'AuthController@roles');
Route::get('/create_role', 'AuthController@create_role');
Route::post('/get_role_list', 'AuthController@get_role_list');
Route::post('/submit_role', 'AuthController@submit_role');
Route::get('/edit_role/{id}', 'AuthController@edit_role');


Route::get('/dashboard', 'DashboardController@index');



/*************************************************
// Frontend URL
**************************************************/
Route::get('/', 'HomeController@index');
Route::get('/404', 'PageController@pagenotfound');
Route::get('/about-us', 'PageController@aboutus');
Route::get('/events', 'PageController@events');
Route::get('/registration', 'PageController@registration');
Route::get('/results', 'PageController@results');
Route::post('/save_registration', 'PageController@save_registration');

Route::post('/get_order_request', 'EventController@get_order_request');
Route::match(array('GET','POST'),'/add_event_result', 'EventController@add_event_result');
Route::post('/get_result', 'EventController@get_result');
Route::post('/submit_result', 'EventController@submit_result');
Route::get('/assign_event', 'EventController@assign_event');
Route::post('/get_assign_event_list', 'EventController@get_assign_event_list');
Route::post('/submit_assign_event', 'EventController@submit_assign_event');
Route::get('/edit_assign_event', 'EventController@edit_assign_event');
Route::get('/create_event', 'EventController@create_event');
Route::get('/edit_event/{id}', 'EventController@edit_event');
Route::post('/submit_event', 'EventController@submit_event');
Route::post('/event_list', 'EventController@event_list');
Route::get('/event_listing', 'EventController@event_listing');
Route::get('/request_certificate', 'EventController@request_certificate');
Route::get('/user_event', 'EventController@user_event');
Route::post('/get_user_event_list', 'EventController@get_user_event_list');