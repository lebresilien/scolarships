<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Services\Service;
use Illuminate\Support\Str;
use App\Models\{ Sequence, Academy, Section, Classroom, Group };

class SequenceController extends Controller
{
    use ApiResponser;

    private $service;
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $current_id = $this->service->currentAcademy($request)->id;

        $academy = Academy::find($current_id);

        return $academy->sequences;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $this->service->currentAcademy($request)->id;

        $sequence = Sequence::where('name', $request->name)->first();

        if($sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "La Sequence existe deja "
            ]
        ], 400);

        Sequence::create([
            "name" => $request->name,
            "slug" => Str::slug($request->name, '-'),
            "academy_id" => $id,
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
    public function update(Request $request, $id)
    {
        $current_id = $this->service->currentAcademy($request)->id;

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sequences,name,' .$current_id],
        ]);

        $sequence = $this->verify($id);

        $sequence->update($input);

        return $this->success($classroom);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sequence = $this->verify($id);

        $sequence->update([
            "status" => 0
        ]);

        return response()->noContent();
    }

    public function verify($slug) {

        $sequence = Sequence::where('slug', $slug)->first();

        if(!$sequence) return response()->json([
            "message" =>  "Error.",
            "errors" => [
                "message" => "Sequence non trouvée"
            ]
        ], 400);

        return $sequence;
    }

    public function sections($sequence_slug) {

        $sequence = $this->verify($sequence_slug);

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

        return $collection;

    }

    public function groups($sequence_slug, $section_slug) {

        $sequence = $this->verify($sequence_slug);

        $section = Section::where('slug', $section_slug)->first();
        
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

    public function classrooms($sequence_slug, $classroom_slug) {

        $sequence = $this->verify($sequence_slug);

        $group = Group::where('slug', $classroom_slug)->first();

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
