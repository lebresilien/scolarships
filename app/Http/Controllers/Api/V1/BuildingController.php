<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\{ Building, Account };
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Repositories\BuildingRepository;

class BuildingController extends Controller
{
    use ApiResponser;
     /** @var BuildingRepository */
     private $buildingRepository;

    public function __construct(BuildingRepository $buildingRepository) {
        $this->buildingRepository = $buildingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $buildings = $this->buildingRepository->list($request);
        return [
            "state" => $buildings
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
            'name' => ['required', 'string', 'max:255', 'unique:buildings,name'],
        ]);

        $input = $request->all();
        $input['slug'] = Str::slug($request->name, '-');
        $input['account_id'] = $request->user()->accounts[0]->id;

        $building = $this->buildingRepository->create($input);

        return $this->success($building, 'ajout');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $building = $this->verify($slug);

        if(!$building)  return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Batiment non trouvé"
            ]
        ], 400);

        $collection = collect([]);

        foreach($building->classrooms as $classroom) {
            $collection->push([
                'name' => $classroom->name,
                'description' => $classroom->description
            ]);
        }

        return $this->success($collection);
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
        $building = $this->verify($id);

        if(!$building)  return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Batiment non trouvé"
            ]
        ], 400);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:buildings,name,'.$building->id]
        ]);
        
        $input = $request->all();
        $input['slug'] = Str::slug($input['name'], '-');

        $this->buildingRepository->update($input, $id);

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

            $building = $this->buildingRepository->find($id);

            if(!$building) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);

            if($building->classrooms->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $id) {
            $this->buildingRepository->delete($id);
        }

        return response()->noContent();
    }

    private function verify($id) {
        return $this->buildingRepository->find($id);
    }
}
