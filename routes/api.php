<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\InvitationController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\SectionController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\BuildingController;
use App\Http\Controllers\Api\V1\ClassroomController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\AcademyController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\TransactionController;
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

Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum' ],  function($route) {

    $route->post('invitation', InvitationController::class);
    $route->put('users', [UserController::class, 'update']);
    
    $route->post('sections', [SectionController::class, 'store']);
    $route->get('sections', [SectionController::class, 'index']);

    $route->post('groups', [GroupController::class, 'store']);
    $route->get('groups', [GroupController::class, 'index']);

    $route->post('buildings', [BuildingController::class, 'store']);
    $route->get('buildings', [BuildingController::class, 'index']);

    $route->post('classrooms', [ClassroomController::class, 'store']);
    $route->get('classrooms', [ClassroomController::class, 'index']);

    $route->get('primary-statistics', [DashboardController::class, 'primary_statistics']);
    $route->get('groups/classrooms', [GroupController::class, 'groups_classrooms']);
    
    $route->post('courses', [CourseController::class, 'store']);
    $route->get('courses', [CourseController::class, 'index']);
    $route->get('courses/{slug}', [CourseController::class, 'show']);
    $route->put('courses/{slug}', [CourseController::class, 'update']);
    $route->delete('courses/{slug}', [CourseController::class, 'destroy']);

    $route->post('academies', [AcademyController::class, 'store']);
    $route->get('academies', [AcademyController::class, 'index']);
    $route->put('academies/{id}', [AcademyController::class, 'update']);

    $route->get('students', [StudentController::class, 'index']);
    $route->get('students/{slug}', [StudentController::class, 'show']);
    $route->post('students', [StudentController::class, 'store']);
    $route->put('students/{slug}', [StudentController::class, 'update']);

    $route->post('transactions', [TransactionController::class, 'store']);
});

Route::post('v1/registration', RegistrationController::class);
Route::get('v1/accounts/{id}', [AccountController::class, 'show']);


/* Route::group(['prefix' => 'v1'], function($route) {
    $route->post('registration', RegistrationController::class);
}); */


 
