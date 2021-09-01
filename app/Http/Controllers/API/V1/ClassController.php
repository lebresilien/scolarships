<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Classe;
use Illuminate\Support\Str;

class ClassController extends Controller
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
     *     path="/api/v1/classes",
     *     tags={"Class"},
     *     summary="Creating new Class",
     *     description="Creating new Class",
     *     @OA\RequestBody(
     *         description="Class Form create",
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
     *                      property="block_id",
     *                      description="ID of block",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="groupe_id",
     *                      description="ID of groupe",
     *                      type="integer",
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
                'block_id' => ['exists:blocks,id'],
                'groupe_id' => ['exists:groupes,id'],
            ])->validate();

        $classe = Block::create([
            "name" => $request->name,
            "block_id" => $request->block_id,
            "groupe_id" => $request->groupe_id,
            "description" => $request->description, 
            "slug" => Str::slug($request->name).'-'.uniqid() 
        ]);

        return $this->sendResponse($classe, "class created succefully");
    }

    /**
     * @OA\get(
     *     path="/api/v1/classes/{slug}",
     *     tags={"Classes"},
     *     summary="Details classe",
     *     description="Show details Classe",
     *     @OA\Parameter(
     *          name="slug",
     *          description="Classe ID identification",
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
        $classe = Classe::where('slug', $slug)->first();
        if(!$classe) return response()->json(['message' => 'Classe not found']);
        return $this->sendResponse($classe, "Classe Details");
    }

   /**
     * @OA\put(
     *     path="/api/v1/classes/{id}",
     *     tags={"Classes"},
     *     summary="Updating classe information",
     *     description="Upadting classe information",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID classe identification",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Form classe updating",
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
     *                      property="block_id",
     *                      description="block id",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="groupe_id",
     *                      description="groupe id",
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
        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'groupe_id' => ['exists:groupes,id'],
                'block_id' => ['exists:block,id'],
            ])->validate();

        $classe = Classe::findOrFail($id);
        $this->authorize('update', $classe);

        $classe->update([
            'name' => $request->name,
            'block_id' => $request->block_id,
            'groupe_id' => $request->groupe_id,
            'description' => $request->description,
            'slug' => Str::slug($request->name).'-'.uniqid()
        ]);

        return $this->sendResponse($classe, "classe updated succefully");
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/classes/{id}",
     *     tags={"Classes"},
     *     description="delete classe ",
     *     @OA\Parameter(
     *         description="Classe ID to delete",
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
        $classe = Classe::findOrFail($id);
        $this->authorize('update', $classe);
        $classe->delete();
        return $this->sendResponse($classe, "classe deleted succefully");
    }
}
