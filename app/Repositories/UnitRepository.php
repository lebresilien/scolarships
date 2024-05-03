<?php

namespace App\Repositories;

use App\Models\Unit;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class UnitRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'slug',
        'name',
        'group_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Unit::class;
    }

    public function list($request) {

        $units = collect([]);

        $sections = Auth::user()->accounts[0]->sections;

        foreach($sections as $section) {

            if($section->groups) {

                foreach($section->groups as $group) {

                    foreach($group->units as $unit) {

                        $units->push([
                            'id' => strval($unit->id),
                            'name' => $unit->name,
                            'description' => $unit->description,
                            'group' => ['value' => $unit->group->id, 'label' => $unit->group->name],
                            'created_at' => $unit->created_at->format('Y-m-d'),
                        ]);

                    }
                }
            }

        }

        return $units;
    }


}
