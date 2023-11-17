<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{ Note };
use App\Repositories\NoteRepository;
use App\Repositories\SequenceRepository;
use App\Repositories\CourseRepository;
use App\Repositories\ClassroomRepository;
use App\Traits\ApiResponser;

class NoteController extends Controller
{
    use ApiResponser;
    /** @var NoteRepository */
    private $noteRepository;
    private $sequenceRepository;
    private $courseRepository;
    private $classroomRepository;

    public function __construct(
        NoteRepository $noteRepository,
        ClassroomRepository $classroomRepository,
        SequenceRepository $sequenceRepository,
        CourseRepository $courseRepository,
    ) {
        $this->noteRepository = $noteRepository;
        $this->sequenceRepository = $sequenceRepository;
        $this->courseRepository = $courseRepository;
        $this->classroomRepository = $classroomRepository;
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
            'students' => ['array'],
            'students.*.value' => ['required', 'numeric', 'max:20'],
            'students.*.id' => ['required', 'exists:students,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'sequence_id' => ['required', 'exists:sequences,id'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
        ]);

        $input = $request->all();

        /* if($request->user()->hasRole('Enseignant')) $classroom = $request->user()->classroom;
        else $classroom = Classroom::where('slug', $input['classroom_slug'])->first(); */
            
        $classroom = $this->classroomRepository->find($input['classroom_id']);
            
        $sequence = $this->sequenceRepository->find($input['sequence_id']);

        $course = $this->courseRepository->find($input['course_id']);
        
        if(!$sequence || !$course || !$classroom) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Une erreur est survenue"
            ]
        ], 400);

        DB::transaction(function () use ($input, $classroom, $sequence, $course) {

            foreach($input['students'] as $student) {

                Note::updateOrCreate(
                    [
                        "student_id" => $student['id'],
                        "sequence_id" => $sequence->id, 
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
