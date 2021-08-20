<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
$api = app('Dingo\Api\Routing\Router');

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

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'v1','namespace' => 'App\Http\Controllers\API\V1'], function($route){
        $route->get('schools/users', 'SchoolController@userSchool');
        $route->get('schools/{id}', 'SchoolController@show');
        $route->post('schools', 'SchoolController@store');
        $route->delete('schools/{id}', 'SchoolController@destroy');
        $route->get('schools/tests', 'SchoolController@tests');
    });
});

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'v1/auth','namespace' => 'App\Http\Controllers\API\V1\Auth'], function($route){
        $route->post('sign-up', 'AuthenticationController@register');
        $route->post('sign-in', 'AuthenticationController@login');
        $route->get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
        $route->get('email/resend', 'VerificationController@resend')->name('verification.resend');
    });
});

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'v1/auth','namespace' => 'App\Http\Controllers\API\V1\Password'], function($route){
        $route->post('password/email', 'ForgotPasswordController@forgot');
        $route->post('password/reset', 'ForgotPasswordController@reset');
    });
});
