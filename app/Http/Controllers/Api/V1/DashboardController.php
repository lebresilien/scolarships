<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{ User, Section, Building, Group, Classroom, Account };

class DashboardController extends Controller
{
    
    public function primary_statistics(Request $request) {
       
        $account = Account::findOrFail($request->user()->accounts[0]->id);
        $count_users_account = $account->users->count();

        $count_groups_account = 0;
        $count_classroom_account = 0;
        
        foreach($account->sections as $section) {

            $count_groups_account += $section->groups->count();
            
        }

        $data_1 = array("count" => $count_users_account,"title" => "utilisateurs" );
        $data_2 = array("count" => $account->sections->count(),"title" => "sections" );
        $data_3 = array("count" => $count_groups_account,"title" => "groupes" );
        $data_4 = array("count" => $count_classroom_account,"title" => "salles" );
        $data_5 = array("count" => 115,"title" => "students" );
        $data_6 = array("count" => 115,"title" => "teachers" );

        $data = array();
        array_push($data, $data_1);
        array_push($data, $data_2);
        array_push($data, $data_3);
        array_push($data, $data_4);
        array_push($data, $data_5);
        array_push($data, $data_6);

        return response()->json($data);
    }
}
