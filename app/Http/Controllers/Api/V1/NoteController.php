<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ Note, Classroom, Sequence, Course };

class NoteController extends Controller
{
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
            'students' => ['array'],
            'students.*.value' => ['required', 'numeric', 'max:20'],
            'students.*.id' => ['required', 'exists:students,id'],
            'course_slug' => ['required', 'exists:courses,slug'],
            'sequence_slug' => ['required', 'exists:sequences,slug'],
            //'classroom_slug' => ['required', 'exists:classrooms,slug'],
        ]);

        $input = $request->all();

        if($request->user()->hasRole('Enseignant')) $classroom = $request->user()->classroom;
        else $classroom = Classroom::where('slug', $input['classroom_slug'])->first();
            
        $sequence = Sequence::where('slug', $input['sequence_slug'])->first();

        $course = Course::where('slug', $input['course_slug'])->first();

        DB::transaction(function () use ($input, $classroom, $sequence, $course) {

            foreach($input['students'] as $student) {

                Note::updateOrCreate(
                    [
                        "student_id" => $student['id'],
                        "sequence_id" => $sequence->id, 
                        "classroom_id" => $classroom->id,
                        "course_id" => $course->id
                    ],
                    [
                        "value" => $student['value']
                    ]
                );

            }

        });

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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
