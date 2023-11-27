<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AbsentRepository;
use App\Services\Service;
use App\Traits\ApiResponser;

class AbsentController extends Controller
{
    use ApiResponser;
    /** @var AbsentRepository */
    private $absentRepository;
    private $service;

    public function __construct(
        Service $service, 
        AbsentRepository $absentRepository, 
    )
    {
        $this->service = $service;
        $this->absentRepository = $absentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'inscription_id' => ['required', 'exists:inscriptions,id'],
            'day' => ['required', 'date', 'before_or_equal:'. now()->format('Y-m-d')],
            'hour' => ['required', 'integer'],
        ]);

        $input = $request->all();

        $absent = $this->absentRepository->all([
            "inscription_id" => $input['inscription_id'],
            "day" => $input['day'],
            "hour" => $input['hour']
        ])->first();

        if($absent)  return response()->json([
            "errors" => [
                "message" => "Cette absence existe deja."
            ]
        ], 400);

        $absent = $this->absentRepository->create($input);

        return $this->success($absent, 'Ajout');
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
    public function update(Request $request, $id)
    {
        $absent = $this->absentRepository->find($id);

        if(!$absent) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Cette absence n'existe pas"
            ]
        ], 400);

        $request->validate([
            'day' => ['required', 'date', 'before_or_equal:'. now()->format('Y-m-d')],
            'hour' => ['required', 'integer'],
        ]);

        $input = $request->all();

        $this->absentRepository->update($input, $id);

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
            $this->absentRepository->delete($id);
        }

        return response()->noContent();
    }
}
