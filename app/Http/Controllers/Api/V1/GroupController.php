<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;

class GroupController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = array();
        $sections = Auth::user()->accounts[0]->sections;

        foreach($sections as $section) {

            if($section->groups) {

                foreach($section->groups as $group) {

                    array_push($data, $group);
                }
            }
            
        }

        //if(count($data) > 0) return $this->success($data[0]);
        return $this->success($data);
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
            'fees' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        $group = Group::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'description' => $request->description,
            'section_id' => $request->section_id,
            'fees' => $request->fees
        ]);

        //return $this->success($section);
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

    public function groups_classrooms(Request  $request)
    {
        $data = array();
        $sections = $request->user()->accounts[0]->sections;

        foreach($sections as $section) {

            if(count($section->groups) > 0 ) {

                array_push($data, $section);

            }
        }

        return response()->json($data);
    }
}
