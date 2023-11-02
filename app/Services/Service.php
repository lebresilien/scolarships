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
           
            //if(count($groups) > 0 ) {

                foreach($section->groups as $group) {
                    
                    if(count($group->classrooms) > 0) {

                        foreach($group->classrooms as $classroom) {
                            array_push($data, [
                                'id' => $classroom->id,
                                'name' => $classroom->name,
                                'description' => $classroom->description,
                                'slug' => $classroom->slug,
                                'created_at' => $classroom->created_at->format('Y-m-d'),
                                'group' => [
                                    'value' => $classroom->group->id,
                                    'label' => $classroom->group->name,
                                ],
                                'building' => [
                                    'value' => $classroom->building->id,
                                    'label' => $classroom->building->name,
                                ]
                            ]);

                        }
                    }
                    

                }
                
            //}
        }

        return $data;
    }
}