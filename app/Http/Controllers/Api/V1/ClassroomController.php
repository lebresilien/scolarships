<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\{ Action, Classroom, Group, Academy, Note, Sequence, Course };
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;
use App\Services\Service;
use Carbon\Carbon;
use App\Repositories\ClassroomRepository;
use App\Repositories\GroupRepository;
use App\Repositories\BuildingRepository;

class ClassroomController extends Controller
{

    use ApiResponser;
    /** @var ClassroomRepository */
    private $classroomRepository;
    private $buildingRepository;
    private $groupRepository;
    private $service;
    
    public function __construct(Service $service, ClassroomRepository $classroomRepository, GroupRepository $groupRepository, BuildingRepository $buildingRepository)
    {
        $this->service = $service;
        $this->classroomRepository = $classroomRepository;
        $this->buildingRepository = $buildingRepository;
        $this->groupRepository = $groupRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        $buildings = $this->buildingRepository->list($request);
        $groups = $this->groupRepository->list($request);

        $collection = collect([]);
        $group_collection = collect([]);

        foreach($buildings as $building) {
            $collection->push([
                'value' => $building['id'],
                'label' => $building['name']
            ]);
        }

        foreach($groups as $group) {
            $group_collection->push([
                'value' => $group['id'],
                'label' => $group['name']
            ]);
        }

        return [
            'state' => $this->service->classrooms($request),
            'additional' => $group_collection,
            'other' => $collection
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
            'building_id' => ['required', 'exists:buildings,id'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        $input = $request->all();

        $classroom = $this->classroomRepository->all([
            "name" => $input['name'],
            "group_id" => $input['group_id']
        ])->first();

        if($classroom)  return response()->json([
            "errors" => [
                "message" => "Cette salle existe deja."
            ]
        ], 400); 

        $input['slug'] = Str::slug($request->name, '-');

        $classroom = $this->classroomRepository->create($input);

        return $this->success($classroom, 'ajout de classe');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        if($request->user()->hasRole('Enseignant')) $classroom = $request->user()->classroom;
        else $classroom = $this->verify($slug);
        
        $students = $classroom->students->where('pivot.academy_id', $this->service->currentAcademy($request)->id)->where('pivot.status', 1);
        
        $avg = $students->map(function($item, $key) {
            return Carbon::parse($item['born_at'])->age;
        });

        return $this->success([
            "count" => count($students),
            "count_men" => count($students->where('sexe', 'M')),
            "count_girl" => count($students->where('sexe', 'F')),
            "min_age" => Carbon::parse($students->min('born_at'))->age,
            "max_age" => Carbon::parse($students->max('born_at'))->age,
            "average_age" => number_format($avg->avg(), 2)
        ]);
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
            'name' => ['required', 'string', 'max:255'],
            'building_id' => ['required', 'exists:buildings,id'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);
        
        $classroom = $this->verify($id);

        $input = $request->all();

        $this->classroomRepository->update($input, $id);

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

            $classroom = $this->classroomRepository->find($id);

            if(!$classroom) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);

            if($classroom->students->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $id) {
            $this->classroomRepository->delete($id);
        }

        return response()->noContent();
    }

    public function courses(Request $request, $slug) {

        $classroom = $this->verify($slug);

        $sorted = $classroom->group->courses->sortBy('name', SORT_NATURAL);

        $current_id = $this->service->currentAcademy($request)->id;

        $academy = Academy::find($current_id);

        return [
            'sequences' => $academy->sequences,
            'courses' => $sorted->values()->all()
        ];
        
    }

    public function students(Request $request, $slug, $course_slug, $sequence_slug) {

        if($request->user()->hasRole('Enseignant')) $classroom = $request->user()->classroom;
        else $classroom = $this->verify($slug);
            
        $sequence = Sequence::where('slug', $sequence_slug)->first();

        $course = Course::where('slug', $course_slug)->first();
        
        if(!$sequence || !$course) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Une erreur est survenue"
            ]
        ], 400);

        $students = $classroom->students->where('pivot.academy_id', $this->service->currentAcademy($request)->id)
                                ->where('pivot.status', '<>',  2)
                                ->sortBy('name', SORT_NATURAL);

        $data = $students->map(function($item) use ($classroom, $sequence, $course) {

            $note = Note::where([
                ['sequence_id', $sequence->id],
                ['classroom_id', $classroom->id],
                ['course_id', $course->id],
                ['student_id', $item['id']]
            ])->first();

            return [
                "id" => $item['id'],
                "name" => $item['fname']. ' ' . $item['lname'],
                "value" => $note ? $note->value : 0
            ];
        });

        return $this->success($data);
    }

    private function verify($slug) {

        $classroom = Classroom::where('slug', $slug)->first();

        if(!$classroom) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Classroom not found"
            ]
        ], 400);

        return $classroom;

    }

    public function studentList(Request $request) {

        if($request->user()->hasRole('Enseignant')) {
            
            $classroom = $request->user()->classroom;
            
            $students = $classroom->students->where('pivot.academy_id', $this->service->currentAcademy($request)->id)->where('pivot.status', 1);
            
            $data = $students->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->fname,
                    'surname' => $student->lname,
                    'matricule' => $student->matricule,
                    'sexe' => $student->sexe,
                    'born_at' => $student->born_at,
                    'father_name' => $student->father_ame,
                    'mother_name' => $student->mother_name,
                    'fphone' => $student->fphone,
                    'mphone' => $student->mphone,
                    'quarter' => $student->quarter,
                    'born_place' => $student->born_place,
                    'allergy' => $student->allergy,
                ];
            });

            return $data->paginate(10);

        } else {
            return response()->json([
                "message" =>  "Error.",
                "errors" => [
                    "message" => "Vous n'avez pas les permissions requises"
                ]
            ], 400);
        }
    }
}
