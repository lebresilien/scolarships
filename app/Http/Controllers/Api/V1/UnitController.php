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

class UnitController extends Controller
{

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

        $groups = $this->groupRepository->list($request);

        return [
            'state' => $units,
            'additional' => $groups
        ];
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

        $this->authorize('view', $unit);

        if(!$unit) return response()->json([
            "errors" => [
                "message" => "Cette unite d'enseignement n\'existe pas."
            ]
        ], 422);

        return $unit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $unit = $this->unitRepository->all(['slug' => $slug, 'group_id' => $request->group_id ])->first();

        if(!$unit) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "L'enseigment n'existe pas"
            ]
        ], 400);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name,'.$unit->id],
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

        foreach($slugs as $slug) {

            $unit = $this->unitRepository->all(['slug' => $slug])->first();

            if($unit->courses->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $slug) {
            $unit = $this->unitRepository->all(['slug' => $slug])->first();
            $unit->state = false;
            $unit->save();
        }

        return response()->noContent();
    }
}
