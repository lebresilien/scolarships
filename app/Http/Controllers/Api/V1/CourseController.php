<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Repositories\CourseRepository;
use App\Repositories\UnitRepository;
use App\Repositories\GroupRepository;

class CourseController extends Controller
{
    /** @var  CourseRepository */
    private $courseRepository;
    private $unitRepository;

    public function __construct(CourseRepository $courseRepository, UnitRepository $unitRepository, GroupRepository $groupRepository) {
        $this->courseRepository = $courseRepository;
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
        $courses = $this->courseRepository->list($request);
        
        $units = $this->unitRepository->list($request);

        $collection = collect([]);
        $unit_collection = collect([]);

        foreach($units as $unit) {
            $unit_collection->push([
                'value' => $unit['id'],
                'label' => $unit['name']
            ]);
        }

        foreach($courses as $course) {
            $collection->push([
                'id' => $course->id,
                'name' => $course->name,
                'slug' => $course->slug,
                'coeff' => $course->coeff,
                'description' => $course->description,
                'created_at' => $course->created_at->format('Y-m-d'),
                'group' => [
                    'value' => $course->unit->id,
                    'label' => $course->unit->name,
                ],
            ]);
        }

        return [
            'state' => $collection,
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
            'name' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:units,id'],
            'coeff' => ['required', 'numeric'],
        ]);

       /*  DB::beginTransaction();

        try { */

            $founderCourse = $this->courseRepository->all([
                'name' => $request->name,
                'unit_id' => $request->unit_id
            ])->first();

            if($founderCourse)  return response()->json([
                "errors" => [
                    "message" => "Ce cours existe deja."
                ]
            ], 422);

            $input = $request->all();
            $input['slug'] = Str::slug($request->name, '-');

            $this->courseRepository->create($input);
                
            /*  foreach($request->selectedCheckbox as $id) {

                $group = $this->groupRepository->find($id);

                if(!$group) return response()->json([
                    "message" =>  "A group does not exit.",
                    "errors" => [
                        "message" => "A group does not exit"
                    ]
                ], 422);
                
                $course->groups()->attach($id);

            } 
            
            else {
                
                foreach($request->selectedCheckbox as $id) {

                    $founderGroup = $this->courseRepository->find($id);

                    if($founderGroup) {

                        $group = GroupCourse::where([
                            ["course_id", $founderCourse->id],
                            ["group_id", $id]
                        ])->first();

                        if($group) return response()->json([
                            "message" =>  "A group alraedy have the course.",
                            "errors" => [
                                "message" => "A group alraedy have the course"
                            ]
                        ], 422);

                        $founderCourse->groups()->attach($id);

                    } 

                }

            }

            DB::commit();

        }catch(\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        } */

        return response()->noContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $course = $this->courseRepository->all(['slug' => $slug])->with('groups')->first();

        if(!$course) return response()->json([
            "message" =>"Ce cours n'existe pas.",
            "errors" => [
                "message" => "Ce cours n'existe pas."
            ]
        ],422);

        return $course;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:units,id'],
            'coeff' => ['required', 'numeric']
        ]);

        $course = $this->courseRepository->find($id);
        
        if(!$course) return response()->json([
            "message" =>"Ce cours n'existe pas.",
            "errors" => [
                "message" => "Ce cours n'existe pas."
            ]
        ],422);

        $input = $request->all();

        /* $course = $this->courseRepository->all(['name' => $input['name'], 'unit_id' => $input['unit_id']])->first();

        if(!$course) return response()->json([
            "message" =>"Ce cours existe pas.",
            "errors" => [
                "message" => "un cours avec ce nom existe deja."
            ]
        ],422); */

        $input['slug'] = Str::slug($request->name, '-');

        $this->courseRepository->update($input, $id);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = $this->courseRepository->find($id);

        if(!$course) return response()->json([
            "message" =>"Ce cours n'existe pas.",
            "errors" => [
                "message" => "Ce cours n'existe pas."
            ]
        ],422);
        
        return response()->noContent();

    }
}
