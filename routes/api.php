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
use App\Http\Controllers\Api\V1\ExtensionController;
use App\Http\Controllers\Api\V1\UnitController;
use App\Http\Controllers\Api\V1\SequenceController;
use App\Http\Controllers\Api\V1\NoteController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\AuthController;
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

Route::post('/signin', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
                ->middleware('auth:sanctum')
                ->name('log-out');

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum' ],  function($route) {

    $route->post('invitation', InvitationController::class);
    $route->put('users', [UserController::class, 'update']);
    
    $route->post('sections', [SectionController::class, 'store']);
    $route->get('sections', [SectionController::class, 'index']);
    $route->put('sections/{id}', [SectionController::class, 'update']);

    $route->post('groups', [GroupController::class, 'store']);
    $route->get('groups', [GroupController::class, 'index']);
    $route->get('groups_classrooms', [GroupController::class, 'groups_classrooms']);
    $route->get('groups/{id}', [GroupController::class, 'show']);
    $route->put('groups/{id}', [GroupController::class, 'update']);
    $route->delete('groups/{ids}', [GroupController::class, 'destroy']);

    $route->post('buildings', [BuildingController::class, 'store']);
    $route->get('buildings', [BuildingController::class, 'index']);
    $route->get('buildings/{id}', [BuildingController::class, 'show']);
    $route->put('buildings/{id}', [BuildingController::class, 'update']);
    $route->delete('buildings/{ids}', [BuildingController::class, 'destroy']);

    $route->post('classrooms', [ClassroomController::class, 'store']);
    $route->get('classrooms/students', [ClassroomController::class, 'studentList']);
    $route->get('classrooms/{id}', [ClassroomController::class, 'show']);
    $route->get('classrooms', [ClassroomController::class, 'index']);
    $route->get('classrooms/{id}/courses/{course_slug}/sequences/{sequence_slug}/students', [ClassroomController::class, 'students']);
    $route->get('classrooms/{id}/courses', [ClassroomController::class, 'courses']);
    $route->put('classrooms/{id}', [ClassroomController::class, 'update']);
    //$route->get('classrooms/{id}/stats', [ClassroomController::class, 'stats']);
    $route->get('primary-statistics', [DashboardController::class, 'primary_statistics']);
    $route->delete('classrooms/{ids}', [ClassroomController::class, 'destroy']);

    $route->post('courses', [CourseController::class, 'store']);
    $route->get('courses', [CourseController::class, 'index']);
    $route->get('courses/{id}', [CourseController::class, 'show']);
    $route->put('courses/{id}', [CourseController::class, 'update']);
    $route->delete('courses/{id}', [CourseController::class, 'destroy']);

    $route->post('academies', [AcademyController::class, 'store']);
    $route->get('academies', [AcademyController::class, 'index']);
    $route->put('academies/{id}', [AcademyController::class, 'update']);
    $route->delete('academies/{slugs}', [AcademyController::class, 'destroy']);

    $route->get('students', [StudentController::class, 'index']);
    $route->get('students/{id}', [StudentController::class, 'show']);
    $route->post('students', [StudentController::class, 'store']);
    $route->put('students/{id}', [StudentController::class, 'update']);
    $route->delete('students/{id}', [StudentController::class, 'destroy']);
    $route->get('students/{id}/details', [StudentController::class, 'details']);
    $route->get('students/{classroom_id}/{amount}/fees', [StudentController::class, 'fees']);

    $route->post('transactions', [TransactionController::class, 'store']);

    $route->post('extensions', [ExtensionController::class, 'store']);
    $route->delete('extensions', [ExtensionController::class, 'destroy']);
    $route->get('extensions/download/{id}', [ExtensionController::class, 'download']);

    $route->post('units', [UnitController::class, 'store']);
    $route->get('units', [UnitController::class, 'index']);
    $route->get('units/{id}', [UnitController::class, 'show']);
    $route->put('units/{id}', [UnitController::class, 'update']);
    $route->delete('units/{slugs}', [UnitController::class, 'destroy']);

    $route->post('sequences', [SequenceController::class, 'store']);
    $route->get('sequences', [SequenceController::class, 'index']);
    $route->get('sequences/{id}', [SequenceController::class, 'show']);
    $route->get('sequences/{sequence_slug}/sections', [SequenceController::class, 'sections']);
    $route->get('sequences/{sequence_slug}/groups/{section_slug}', [SequenceController::class, 'groups']);
    $route->get('sequences/{sequence_slug}/classrooms/{classroom_slug}', [SequenceController::class, 'classrooms']);
    $route->delete('sequences/{id}', [SequenceController::class, 'destroy']);

    $route->post('notes', [NoteController::class, 'store']);

    $route->post('signature_pad', [AccountController::class, 'signature_pad']);

    $route->get('test', function(Request $request) {
        // Carbon::now()->format('Y-m-d');
        ///return 'fnf';
        //$account_courses = $request->user()->accounts[0]->units;
        return public_path('storage/signatures/').$request->user()->accounts[0]->signature_base64;
     });
});

Route::post('v1/registration', RegistrationController::class);
Route::get('v1/accounts/{id}', [AccountController::class, 'show']);


/* Route::group(['prefix' => 'v1'], function($route) {
    $route->post('registration', RegistrationController::class);
}); */

Route::group(['prefix' => 'v1'], function($route) {
    $route->get('textote', function(Request $request) {
        App::setLocale('fr');
        $locale = App::getLocale();
        $ext = App\Models\Extension::find(1);
        return $ext->created_at->format('d-m-y');
       // Carbon::now()->format('Y-m-d');
       //return $account_courses = $request->user()->accounts[0]->units->courses;
    });
});

Route::get('v1/hello', function() {

   /*  $username = 'dcf5f9f7-eb61-40be-ab3c-557f6b51cbd4';
	$password = '4accc57e7c434f9f823c8644b5a2c03b';
	
	$auth = $username . ':' . $password;
	$credentials = base64_encode($auth);
	
	echo $credentials; */
    //Storage::makeDirectory('public/test');

    $collection = collect(['taylor', 'abigail', null])->map(function ($name) {
        return strtoupper($name);
    })->reject(function ($name) {
        return empty($name);
    });

    return $collection;
});


 
