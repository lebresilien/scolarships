<?php

namespace App\Repositories;

use App\Models\Building;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class BuildingRepository extends BaseRepository
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
        return Building::class;
    }

    public function list($request) {

        $buildings = array();

        foreach(Auth::user()->accounts[0]->buildings as $building) {
            array_push($buildings, [
                'id' => $building->id,
                'name' => $building->name,
                'created_at' => $building->created_at->format('Y-m-d'),
                'slug' => $building->slug,
                'description' => $building->description,
            ]);
        }

        return $buildings;
    }


}
