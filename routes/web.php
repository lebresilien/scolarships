<?php

use Illuminate\Support\Facades\Route;
use App\Models\Classroom;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/test', function () {
  return now()->format('Y');
   
   
  
});


require __DIR__.'/auth.php';
