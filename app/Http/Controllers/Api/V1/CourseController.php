<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\{ Course, Group, GroupCourse };
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array();
        $sections = $request->user()->accounts[0]->sections;

        foreach($sections as $section) {

            if($section->groups) {

                foreach($section->groups as $group) {

                    if($group->courses) {

                        foreach($group->courses as $course)
                        {  
                            array_push($data, $course);
                        }
                    }
                    
                }
            }
            
        }

        $collection = collect($data)->unique('name');
        return $collection->values()->all();
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
            "selectedCheckbox"    => "required|array|min:1",
            "selectedCheckbox.*"  => "required|numeric|distinct",
        ]);

        DB::beginTransaction();

        try {

            $founderCourse = Course::where('name', $request->name)
                                    ->where('unit_id', $request->unit_id)
                                    ->first();
            //$account_courses = $request->user()->accounts[0]->units->courses;

            if(!$founderCourse) {

                $course = Course::create([
                    'name' => $request->name,
                    'unit_id' => $request->unit_id,
                    'slug' => Str::slug($request->name, '-'),
                    'description' => $request->description,
                ]);
                
                foreach($request->selectedCheckbox as $id) {

                    $group = Group::find($id);

                    if(!$group) return response()->json([
                        "message" =>  "A group does not exit.",
                        "errors" => [
                            "message" => "A group does not exit"
                        ]
                    ], 422);
                    
                    $course->groups()->attach($id);

                }
                

            }
            else {

                /* return response()->json([
                    "errors" => [
                        "message" => "Ce cours existe deja."
                    ]
                ], 422); */
                
                foreach($request->selectedCheckbox as $id) {

                    $founderGroup = Group::find($id);

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
        } 

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
        $course = Course::where('slug', $slug)->with('groups')->first();

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
    public function update(Request $request, $slug)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:units,id'],
            "selectedCheckbox"    => "required|array|min:1",
            "selectedCheckbox.*"  => "required|numeric|distinct",
        ]);

        $course = Course::where('slug', $slug)->first();

        if(!$course) return response()->json([
            "message" =>"Ce cours n'existe pas.",
            "errors" => [
                "message" => "Ce cours n'existe pas."
            ]
        ],422);

        $course->groups()->detach();

        foreach($request->selectedCheckbox as $id) {

            $group = Group::find($id);

            if(!$group) return response()->json([
                "message" =>  "A group does not exit.",
                "errors" => [
                    "message" => "A group does not exit"
                ]
            ], 422);
            
            $course->groups()->attach($id);

        }

        $course->update(["name" => $request->name, "unit_id" => $request->unit_id]);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $course = Course::where('slug', $slug)->first();

        if(!$course) return response()->json([
            "message" =>"Ce cours n'existe pas.",
            "errors" => [
                "message" => "Ce cours n'existe pas."
            ]
        ],422);

        
        $course->groups()->detach();
        return response()->noContent();

    }
}
