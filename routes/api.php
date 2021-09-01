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
        $route->get('schools/{slug}', 'SchoolController@show');
        $route->put('schools/{slug}', 'SchoolController@update');
        $route->post('schools', 'SchoolController@store');
        $route->delete('schools/{slug}', 'SchoolController@destroy');
        $route->get('schools/tests', 'SchoolController@tests');

        $route->get('blocks/{slug}', 'BlockController@show');
        $route->put('blocks/{id}', 'BlockController@update');
        $route->post('blocks', 'BlockController@store');
        $route->delete('blocks/{id}', 'BlockController@destroy');

        $route->get('sections/{slug}', 'SectionController@show');
        $route->put('sections/{id}', 'SectionController@update');
        $route->post('sections', 'SectionController@store');
        $route->delete('sections/{id}', 'SectionController@destroy');

        $route->get('classes/{slug}', 'ClassController@show');
        $route->put('classes/{id}', 'ClassController@update');
        $route->post('classes', 'ClassController@store');
        $route->delete('classes/{id}', 'ClassController@destroy');

        $route->post('invite', 'InvitationController@store');
        $route->get('registration/{token}/{role_id}/{classe_id?}', 'InvitationController@registration')
            ->name('registration')->middleware('signed');;
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
    $api->group(['prefix' => 'v1/password','namespace' => 'App\Http\Controllers\API\V1\Password'], function($route){
        $route->post('email', 'ForgotPasswordController@forgot');
        $route->post('reset', 'ForgotPasswordController@reset')->name('password.reset');
    });
});
