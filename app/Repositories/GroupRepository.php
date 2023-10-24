<?php

namespace App\Repositories;

use App\Models\Group;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class GroupRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'slug',
        'name'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Group::class;
    }

    public function list($request) {

        $groups = array();

        $sections = Auth::user()->accounts[0]->sections;

        foreach($sections as $section) {

            if($section->groups) {

                foreach($section->groups as $group) {
                    array_push($groups, [
                        'id' => $group->id,
                        'name' => $group->name,
                        'created_at' => $group->created_at->format('Y-m-d'),
                        'fees' => $group['fees'],
                        'slug' => $group->slug,
                        'description' => $group->description,
                        'group' => [
                            'value' => $group->section->id,
                            'label' => $group->section->name,
                        ],
                    ]);
                }

            }
            
        }

        return $groups;
    }


}
