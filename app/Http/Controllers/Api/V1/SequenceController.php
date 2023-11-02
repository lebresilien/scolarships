<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Services\Service;
use Illuminate\Support\Str;
use App\Models\{ Sequence, Academy, Section, Classroom, Group };
use App\Repositories\SequenceRepository;
use App\Repositories\AcademyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\SectionRepository;

class SequenceController extends Controller
{
    use ApiResponser;
    /** @var SequenceRepository */
    private $sequenceRepository;
    private $service;
    private $academyRepository;
    private $groupRepository;
    private $sectionRepository;
    
    public function __construct(Service $service, SequenceRepository $sequenceRepository, AcademyRepository $academyRepository, GroupRepository $groupRepository, SectionRepository $sectionRepository)
    {
        $this->service = $service;
        $this->sequenceRepository = $sequenceRepository;
        $this->academyRepository = $academyRepository;
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
        $current_id = $this->service->currentAcademy($request)->id;

        $academy = $this->academyRepository->find($current_id);

        return [
            "state" => $academy->sequences
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

        $id = $this->service->currentAcademy($request)->id;
        $input = $request->all();

        $sequence = $this->sequenceRepository->all([
            'name' => $input['name'],
            'academy_id' => $id
        ])->first();

        if($sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "La Sequence existe deja "
            ]
        ], 400);

        $input["academy_id"] = $id;
        $input["slug"] = Str::slug($request->name, '-');

        $sequence = $this->sequenceRepository->create($input);

        return $this->success($sequence, 'ajout de sequence');
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $sequence = $this->sequenceRepository->find($id);

        if(!$sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Sequence non trouvée"
            ]
        ], 400);

        $current_id = $this->service->currentAcademy($request)->id;

        $input = $request->all();

        $this->sequenceRepository->update($input, $id);

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

            $sequence = $this->sequenceRepository->find($id);

            if(!$sequence) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);

            if($sequence->notes->count() > 0) return response()->json([
                "message" =>  "Erreur.",
                "errors" => [
                    "message" => "Vous ne pouvez pas effectuer cette opération."
                ]
            ], 400);
        }

        foreach($slugs as $id) {
            $this->sequenceRepository->delete($id);
        }

        return response()->noContent();
    }

    public function verify($id) {

        $sequence = $this->sequenceRepository->find($id);

        if(!$sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Sequence non trouvée"
            ]
        ], 400);

        return $sequence;
    }

    public function sections($sequence_id) {

        $sequence = $this->sequenceRepository->find($sequence_id);

        if(!$sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Sequence non trouvée"
            ]
        ], 400);

        $collection = collect([]);

        foreach($sequence->academy->account->sections as $section) {

            $result = $section->notes()->where('sequence_id', $sequence->id)->get()->groupBy('student_id')->map(function($item) {
                return [
                    'note' => $item->sum('value') / $item->count(),
                    'name' => $item[0]->student->lname . ' ' .$item[0]->student->fname
                ];
            });
            
            $collection->push([
                'id' => $section->slug,
                'name' => $section->name,
                'data' => $result->count() > 0 ? [
                    "max" => $result->firstWhere('note', $result->max('note')),
                    "min" => $result->firstWhere('note', $result->min('note')),
                    "average_note" => $result->avg('note'),
                    "success_percent" => $result->where('note', '>', 10)->count() * 100 / $result->count()
                ] : [],
            ]);

        }

        return [
            'data' => $collection,
            'title' => $sequence->name
        ];

    }

    public function groups($sequence_id, $section_id) {

        $sequence = $this->verify($sequence_id);

        $section = $this->sectionRepository->find($section_id);
        
        if(!$section) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Section non trouvée"
            ]
        ], 400);

        $collection = collect([]);

        foreach($section->groups as $group) {

            $result = $group->notes()->where('sequence_id', $sequence->id)->get()->groupBy('student_id')->map(function($item) {
                return [
                    'note' => $item->sum('value') / $item->count(),
                    'name' => $item[0]->student->lname . ' ' .$item[0]->student->fname
                ];
            });

            $collection->push([
                'id' => $group->slug,
                'name' => $group->name,
                'data' => $result->count() > 0 ? [
                    "max" => $result->firstWhere('note', $result->max('note')),
                    "min" => $result->firstWhere('note', $result->min('note')),
                    "average_note" => $result->avg('note'),
                    "success_percent" => $result->where('note', '>', 10)->count() * 100 / $result->count()
                ] : [],
            ]);
        }

        return $collection;
    }

    public function classrooms($sequence_id, $classroom_id) {

        $sequence = $this->sequenceRepository->find($sequence_id);

        if(!$sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Sequence non trouvée"
            ]
        ], 400);

        $group = $this->groupRepository->find($classroom_id);

        if(!$group) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Classe non trouvée"
            ]
        ], 400);

        $collection = collect([]);

        foreach($group->classrooms as $classroom) {

            $result = $classroom->notes()->where('sequence_id', $sequence->id)->get()->groupBy('student_id')->map(function($item) {
                return [
                    'note' => $item->sum('value') / $item->count(),
                    'name' => $item[0]->student->lname . ' ' .$item[0]->student->fname
                ];
            });

            $collection->push([
                'id' => $classroom->slug,
                'name' => $classroom->name,
                'data' => $result->count() > 0 ? [
                    "max" => $result->firstWhere('note', $result->max('note')),
                    "min" => $result->firstWhere('note', $result->min('note')),
                    "average_note" => $result->avg('note'),
                    "success_percent" => $result->where('note', '>', 10)->count() * 100 / $result->count()
                ] : [],
            ]);
        }

        return $collection;

    }
}
