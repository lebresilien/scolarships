<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;
use App\Repositories\GroupRepository;
use App\Repositories\SectionRepository;

class GroupController extends Controller
{
    use ApiResponser;
    /** @var GroupRepository */
    private $groupRepository;
    private $sectionRepository;

    public function __construct(GroupRepository $groupRepository, SectionRepository $sectionRepository) {
        $this->groupRepository = $groupRepository;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = $this->groupRepository->list($request);
        $sections = $this->sectionRepository->list($request);

        $unit_collection = collect([]);

        foreach($sections as $section) {
            $unit_collection->push([
                'value' => $section['id'],
                'label' => $section['name']
            ]);
        }

        return [
            'state' => $groups,
            'additional' => $unit_collection
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
            'name' => ['required', 'string', 'max:255', 'unique:groups'],
            'fees' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'section_id' => ['required', 'exists:sections,id']
        ]);

        $input = $request->all();
        $input['slug'] = Str::slug($request->name, '-');

        $group = $this->groupRepository->create($input);

        return $this->success($group, 'ajout');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = $this->groupRepository->find($id);

        if(!$group) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Groupe non trouvé"
            ]
        ], 400);

        $data = $group->classrooms->map(function($classroom) {
            return [
                'name' => $classroom->name,
                'description' => $classroom->description,
                'slug' => $classroom->slug
            ];
        });
        
        return $this->success(["data" => $data, "name" => $group->name], 'Details');
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
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name,'.$id],
            'fees' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'section_id' => ['required', 'exists:sections,id']
        ]);
        
        $group = $this->groupRepository->find($id);

        if(!$group) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Groupe non trouvé"
            ]
        ], 400);

        $input = $request->all();

        $input['slug'] = $group->slug;

        $this->groupRepository->update($input, $id);

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

            $group = $this->groupRepository->find($id);

            if(!$group) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);

            if($group->units->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $id) {
            $unit = $this->groupRepository->delete($id);
        }

        return response()->noContent();
    }

    public function groups_classrooms(Request $request)
    {
        $data = array();
        $sections = $request->user()->accounts[0]->sections;

        foreach($sections as $section) {

            if(count($section->groups) > 0 ) {

                array_push($data, $section);

            }
        }

        $units = $request->user()->accounts[0]->units;

        return response()->json([
            'classrooms' => $data,
            'units' => $units
        ]);
    }

    private function verify($id) {
        return $this->groupRepository->find($id);
    }
}
