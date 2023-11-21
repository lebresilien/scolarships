<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\{ Section, Account };
use App\Traits\ApiResponser;
use Illuminate\Support\Str;
use App\Repositories\SectionRepository;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{

    use ApiResponser;
    /** @var SectionRepository */
    private $sectionRepository;

    public function __construct(SectionRepository $sectionRepository) {
        $this->sectionRepository = $sectionRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = $this->sectionRepository->list();
       
        return  [
            'state' => $sections
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
            'name' => ['required', 'string', 'max:255', 'unique:sections'],
        ]);

        $input = $request->all();
        $input['slug'] = Str::slug($request->name, '-');
        $input['account_id'] = Auth::user()->accounts[0]->id;

        $section = $this->sectionRepository->create($input);

        return $this->success($section, 'ajout');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $section = $this->sectionRepository->find($id);

        if(!$section) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "La section n'existe pas"
            ]
        ], 400);

        $data = $section->groups->map(function($group) {
            return [
                'value' => $group->id,
                'label' => $group->name
            ];
        });

        return $this->success($data, 'Details');
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
        $section = $this->sectionRepository->find($id);

        if(!$section) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "La section n'existe pas"
            ]
        ], 400);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sections,name,'.$section->id],
        ]);

        $input = $request->all();
        $input['slug'] = Str::slug($input['name'], '-');
        $input['account_id'] = Auth::user()->accounts[0]->id;

        $this->sectionRepository->update($input, $id);

        return response()->noContent();
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $section = $this->sectionRepository->find($id);

        if(!$section) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "La section n'existe pas"
            ]
        ], 400);

        $section->status = false;
        $section->save();

        return response()->noContent();
    }
}
