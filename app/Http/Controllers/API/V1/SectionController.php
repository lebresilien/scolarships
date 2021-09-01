<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Section};
use Illuminate\Support\Str;

class SectionController extends Controller
{
    
    public function index()
    {
        
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sections",
     *     tags={"Sections"},
     *     summary="Creating new Section",
     *     description="Creating new Section",
     *     @OA\RequestBody(
     *         description="Section Form create",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      description="Nom.",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="school_id",
     *                      description="ID of school.",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="Description",
     *                      type="string",
     *                  ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succsess response"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'school_id' => ['exists:schools,id'],
            ])->validate();

        $section = Section::create([
            "name" => $request->name,
            "school_id" => $request->school_id,
            "description" => $request->description, 
            "slug" => Str::slug($request->name).'-'.uniqid() 
        ]);

        return $this->sendResponse($section, "section created succefully");
    }

    /**
     * @OA\get(
     *     path="/api/v1/sections/{slug}",
     *     tags={"Sections"},
     *     summary="Details section",
     *     description="Show details section",
     *     @OA\Parameter(
     *          name="slug",
     *          description="Section ID identification",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="succsess response"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function show($slug)
    {
        $section = Section::where('slug', $slug)->first();
        if(!$section) return response()->json(['message' => 'Section not found']);
        return $this->sendResponse($section, "Section Details");
    }

    /**
     * @OA\put(
     *     path="/api/v1/sections/{id}",
     *     tags={"Sections"},
     *     summary="Updating section information",
     *     description="Upadting section information",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID section identification",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Form section updating",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      description="name .",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="school_id",
     *                      description="school id",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="Description section",
     *                      type="string",
     *                      required=false,
     *                  ),  
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succsess response"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $section->update([
            'name' => $request->name,
            'school_id' => $request->school_id,
            'description' => $request->description,
            'slug' => Str::slug($request->name).'-'.uniqid()
        ]);

        return $this->sendResponse($section, "section updated succefully");
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/schools/{id}",
     *     tags={"Sections"},
     *     description="delete section ",
     *     @OA\Parameter(
     *         description="Section ID to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="School deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();
        return $this->sendResponse($section, "section deleted succefully");
    }
}
