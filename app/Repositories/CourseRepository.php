<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\BaseRepository;

class CourseRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'unit_id',
        'slug'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Course::class;
    }

    public function list($request) {

        $data = array();

        $sections = $request->user()->accounts[0]->sections;

        foreach($sections as $section) {

            if($section->groups) {

                foreach($section->groups as $group) {

                    if($group->units) {

                        foreach($group->units as $unit)
                        {  
                            foreach($unit->courses as $course) {
                                array_push($data, $course);
                            }
                        }
                    }
                    
                }
            }
            
        }

        return collect($data)->unique('name');
        
    }

}
