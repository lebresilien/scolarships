<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Academy, Classroom };
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Repositories\AcademyRepository;

class AcademyController extends Controller
{
    /** @var AcademyRepository */
    private $academyRepository;

    public function __construct(AcademyRepository $academyRepository) {
        $this->academyRepository = $academyRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
  
        $academies = $this->academyRepository->all(['account_id' => $request->$request->user()->accounts[0]->id]);
       
        return [
            'state' => $academies
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
            'name' => ['required', 'string', 'max:255']
        ]);
        
        $founderAcademy = $this->academyRepository->all([
            "name", $request->name,
            "account_id", $request->user()->accounts[0]->id
        ])->first();

        if($founderAcademy) return response()->json([
            "message" =>  "Academy name already exists.",
            "errors" => [
                "message" => "Academy name already exists"
            ]
        ], 422);

        $founder_active_academy = $this->academyRepository->all([
           "account_id", $request->user()->accounts[0]->id,
           "status", true
        ])->first();

        if($founder_active_academy)  return response()->json([
            "message" =>  "An active academy already exists.",
            "errors" => [
                "message" => "An active academy already exists."
            ]
        ], 422);

        $this->academyRepository->create([
            "name" => $request->name,
            "slug" => Str::slug($request->name, '-'),
            "account_id" => $request->user()->accounts[0]->id
        ]);

        return response()->noContent();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        $academy = $this->academyRepository->all([
            "slug" => $request->slug,
        ])->first();

        if($academy)  return response()->json([
            "message" =>  "Erreur.",
            "errors" => [
                "message" => "Aucun element trouvé."
            ]
        ], 422);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:academies,name,'.$academy->id],
        ]);

        $academy->status = false;
        $academy->save();

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slugs)
    {
       $slugs = explode(';', $slugs);
       
        foreach($slugs as $slug) {

            $academy = $this->academyRepository->all(['slug' => $slug])->first();

            if($academy->inscriptions->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $slug) {
            $academy = $this->academyRepository->all(['slug' => $slug])->first();
            $academy->state = false;
            $academy->save();
        }

        return response()->noContent();
    }

    public function switchStatus($id) {

        $academy = $this->academyRepository->find($id);
        $academy->status = false;
        $academy->save();
    }
}
