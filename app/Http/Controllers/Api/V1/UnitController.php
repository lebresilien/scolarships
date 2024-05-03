<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UnitRepository;
use App\Repositories\GroupRepository;
use App\Traits\ApiResponser;

class UnitController extends Controller
{
    use ApiResponser;
    /** @var  UnitRepository */
    private $unitRepository;
    private $groupRepository;

    public function __construct(UnitRepository $unitRepository, GroupRepository $groupRepository) {
        $this->unitRepository = $unitRepository;
        $this->groupRepository = $groupRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $units = $this->unitRepository->list($request);

      /*   $groups = $this->groupRepository->list($request);

        $collection = collect([]);

        foreach($groups as $group) {
            $collection->push([
                'value' => $group['id'],
                'label' => $group['name']
            ]);
        } */

        return $this->success($units, 'list');
    }

    public function create(Request $request) {

        $data = collect([]);

        $units = $this->unitRepository->list($request);

        foreach($units as $unit) {
            $data->push([
                'value' => strval($unit['id']),
                'label' => $unit['name']
            ]);
        }

        return $this->success($data, 'list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        $founderUnit = $this->unitRepository->all(['name' => $request->name, 'group_id' => $request->group_id])->first();

        if($founderUnit) return response()->json([
            "errors" => [
                "message" => "Cet unité d'enseignement existe deja."
            ]
        ],422);

        $unit = $this->unitRepository->create([
            "name" => $request->name,
            "slug" => Str::slug($request->name),
            "group_id" => $request->group_id,
            "description" => $request->description,
        ]);

        return $this->success($unit, 'ajout');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = $this->unitRepository->find($id);

       // $this->authorize('view', $unit);

        if(!$unit) return response()->json([
            "errors" => [
                "message" => "Cette unite d'enseignement n\'existe pas."
            ]
        ], 422);

        return $this->success(["name" => $unit->name, "data" => $unit->courses], 'show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $unit = $this->unitRepository->find($id);

        if(!$unit) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "L'Enseignement n'existe pas"
            ]
        ], 400);

        $request->validate([
            //'name' => ['required', 'string', 'max:255', 'unique:units,name,'.$unit->id],
            'group_id' => ['required', 'exists:groups,id']
        ]);

        $input = $request->all();
        $input['slug'] = Str::slug($input['name'], '-');
        $input['account_id'] = Auth::user()->accounts[0]->id;

        $this->unitRepository->update($input, $unit->id);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ids)
    {
        $slugs = explode(';', $ids);

        foreach($slugs as $id) {

            $unit = $this->unitRepository->find($id);

            if($unit->courses->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $id) {
            $unit = $this->unitRepository->find($id);
            $unit->delete();
        }

        return response()->noContent();
    }
}
