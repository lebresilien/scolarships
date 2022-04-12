<?php

namespace App\Services;
use App\Models\{ Academy, Group };

class Service {

    public function generate() {

        $year = date('Y');
       /*  $month = date('m');
        $day = date('d'); */

        return $year.rand(1000000, 9999999);

    }

    public function currentAcademy($request) {

        $current = Academy::whereStatus(true)
                            ->where("account_id", $request->user()->accounts[0]->id)
                            ->first();
        return $current;
    }

    public function classrooms($request) {
        
        $data = array();
        $sections = $request->user()->accounts[0]->sections;

        foreach($sections as $section) {

            $groups = Group::where('section_id', $section->id)->get();
           
            if(count($groups) > 0 ) {

                foreach($groups as $group) {
                    
                    if(count($group->classrooms) > 0) {

                        foreach($group->classrooms as $classroom) {

                            array_push($data, $classroom);

                        }
                    }
                    

                }
                
            }
        }

        return response()->json($data);
    }
}