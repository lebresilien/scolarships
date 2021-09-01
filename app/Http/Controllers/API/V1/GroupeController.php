<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupeController extends Controller
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
     * @OA\Post(
     *     path="/api/v1/groupes",
     *     tags={"Groupes"},
     *     summary="Creating new Groupe",
     *     description="Creating new Groupe",
     *     @OA\RequestBody(
     *         description="Groupe Form create",
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
                'fees' => ['required', 'regex:/^[0-9]+(\.[0-9]{1,2}?$/'],
                'school_id' => ['exists:schools,id'],
            ])->validate();

        $groupe = Groupe::create([
            "name" => $request->name,
            "fees" => $request->fees,
            "school_id" => $request->school_id,
            "description" => $request->description, 
            "slug" => Str::slug($request->name).'-'.uniqid() 
        ]);

        return $this->sendResponse($groupe, "block created succefully");
    }

    /**
     * @OA\get(
     *     path="/api/v1/groupes/{slug}",
     *     tags={"Groupes"},
     *     summary="Details groupe",
     *     description="Show details Groupe",
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
        $groupe = Groupe::where('slug', $slug)->first();
        if(!$groupe) return response()->json(['message' => 'Groupe not found']);
        return $this->sendResponse($groupe, "Block Details");
    }

     /**
     * @OA\put(
     *     path="/api/v1/groupes/{id}",
     *     tags={"Groupes"},
     *     summary="Updating groupe information",
     *     description="Upadting groupe information",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID groupe identification",
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
     *                      property="fees",
     *                      description="school fees",
     *                      type="double",
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
        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'fees' => ['required', 'regex:/^[0-9]+(\.[0-9]{1,2}?$/'],
            ])->validate();

        $block = Block::findOrFail($id);
        $this->authorize('update', $block);

        $section->update([
            'name' => $request->name,
            'fees' => $request->fees,
            'description' => $request->description,
            'slug' => Str::slug($request->name).'-'.uniqid()
        ]);

        return $this->sendResponse($section, "section updated succefully");
    }

     /**
     * @OA\Delete(
     *     path="/api/v1/groupes/{id}",
     *     tags={"Groupes"},
     *     description="delete groupe ",
     *     @OA\Parameter(
     *         description="Groupe ID to delete",
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
     *         description="Block deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function destroy($id)
    {
        $groupe = Groupe::findOrFail($id);
        $this->authorize('update', $groupe);
        $groupe->delete();
        return $this->sendResponse($groupe, "groupe deleted succefully");
    }
}
