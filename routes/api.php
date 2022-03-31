<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('category_list','ApiController@category_list');
Route::get('terms','ApiController@terms');


Route::get('subcategory_list','ApiController@subcategory_list');
Route::get('graph_list','ApiController@graph_list')->middleware('auth:api');

Route::post('register','ApiController@register');
Route::post('login','ApiController@login');
Route::post('verifyUser','ApiController@verifyUser');

Route::post('socialLogin','ApiController@socialLogin');

Route::get('profile','ApiController@profile')->middleware('auth:api');
Route::post('update_profile','ApiController@profile')->middleware('auth:api');

Route::post('create_room','ApiController@create_room')->middleware('auth:api');

Route::post('update_device','ApiController@update_device')->middleware('auth:api');

Route::get('check_username','ApiController@check_username')->middleware('auth:api');  
Route::get('check_unique_id','ApiController@check_unique_id');  

Route::post('forgotPassword','ApiController@forgotPassword');
Route::post('resetPassword','ApiController@resetPassword');

Route::get('user_search','ApiController@user_search')->middleware('auth:api');

Route::get('logout','ApiController@logout')->middleware('auth:api');

Route::get('home_list','ApiController@home_list')->middleware('auth:api');
Route::get('block_list','ApiController@block_list')->middleware('auth:api');


Route::post('userNotify','ApiController@userNotify')->middleware('auth:api');
Route::post('like','ApiController@like')->middleware('auth:api');
Route::post('favourite','ApiController@favourite')->middleware('auth:api');

Route::delete('deleteAccount','ApiController@deleteAccount')->middleware('auth:api');


Route::post('setpreferences','ApiController@setpreferences')->middleware('auth:api');
Route::get('setpreferences','ApiController@setpreferences')->middleware('auth:api');

Route::get('gallery','ApiController@gallery')->middleware('auth:api');
Route::post('create_post','ApiController@createPost')->middleware('auth:api');
Route::delete('gallery','ApiController@gallery')->middleware('auth:api');

Route::post('make_default','ApiController@make_default')->middleware('auth:api');

Route::post('visibility','ApiController@visibility')->middleware('auth:api');

Route::get('match','ApiController@match')->middleware('auth:api');
Route::delete('match','ApiController@match')->middleware('auth:api');

Route::get('pending_match','ApiController@pending_match')->middleware('auth:api');

Route::post('report','ApiController@report')->middleware('auth:api');

Route::get('user_detail','ApiController@user_detail')->middleware('auth:api');

Route::get('recommend_list','ApiController@recommend_list')->middleware('auth:api');
Route::get('partner_detail','ApiController@partner_detail')->middleware('auth:api');


Route::get('subscriptionsList','ApiController@subscriptionsList');
Route::post('pendingSubscriptionPlan','ApiController@pendingSubscriptionPlan')->middleware('auth:api');
Route::post('androidSubscreption','ApiController@androidSubscreption')->middleware('auth:api');
Route::get('subscriptions','ApiController@subscriptions')->middleware('auth:api');
Route::get('cronJobForSubscreption','ApiController@cronJobForSubscreption');



// twilio
Route::post('chat_user', "ApiController@chat_user")->middleware('auth:api');
Route::get('chat_token','ApiController@chat_token')->middleware('auth:api');
Route::post('chat_post_event','ApiController@chat_post_event');
Route::post('chat_pre_event','ApiController@chat_pre_event');
Route::get('chat_update_uername','ApiController@chat_update_uername');
Route::post('addchatuser','ApiController@addchatuser');
Route::post('contact','ApiController@contact');

Route::get('check_pending','ApiController@check_pending');
Route::get('update_previous','ApiController@update_previous');

Route::get('notification_match_detail','ApiController@notification_match_detail')->middleware('auth:api');
// Question Answer
Route::get('question','ApiController@question')->middleware('auth:api');
Route::post('answer','ApiController@answer')->middleware('auth:api');
Route::delete('answer_delete','ApiController@answer_delete')->middleware('auth:api');

Route::post('chip','ApiController@chip')->middleware('auth:api');
Route::get('chip_list','ApiController@chip_list')->middleware('auth:api');

Route::get('subscriptionsList','ApiController@subscriptionsList');
Route::post('pendingSubscriptionPlan','ApiController@pendingSubscriptionPlan')->middleware('auth:api');
Route::get('cronJobForaddList','ApiController@cronJobForaddList');
Route::get('cronJobForaddClose','ApiController@cronJobForaddClose');
Route::get('cronJobForSubscreption','ApiController@cronJobForSubscreption');

Route::post('androidSubscreption','ApiController@androidSubscreption')->middleware('auth:api');


/*
Route::middleware('auth')->group(function () {
    Route::get('profile', [App\Http\Controllers\ApiController::class, 'profile'])->name('profile');
    });*/