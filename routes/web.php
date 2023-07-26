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

    User::find(1)->update(['password' => Hash::make('12345678')]);
   /*  $classroom = Classroom::find(2);
    
    $data =  $classroom->notes->groupBy('student_id');
    return $data->map(function($item) {
        return $item->sum('value');
    });
    return $data->all();
    $data = Note::select(DB::raw('sum(value) as total'))->groupBy('student_id')->get();
 return ($data); */
  
});


require __DIR__.'/auth.php';
