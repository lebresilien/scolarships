<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Block};
use Illuminate\Support\Str;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * @OA\Post(
     *     path="/api/v1/blocks",
     *     tags={"Blocks"},
     *     summary="Creating new Block",
     *     description="Creating new Block",
     *     @OA\RequestBody(
     *         description="Block Form create",
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

        $block = Block::create([
            "name" => $request->name,
            "school_id" => $request->school_id,
            "description" => $request->description, 
            "slug" => Str::slug($request->name).'-'.uniqid() 
        ]);

        return $this->sendResponse($block, "block created succefully");
    }

    /**
     * @OA\get(
     *     path="/api/v1/blocks/{slug}",
     *     tags={"Blocks"},
     *     summary="Details block",
     *     description="Show details Block",
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
        $block = Block::where('slug', $slug)->first();
        if(!$block) return response()->json(['message' => 'Block not found']);
        return $this->sendResponse($block, "Block Details");
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
        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                //'school_id' => ['exists:schools,id'],
            ])->validate();

        $block = Block::findOrFail($id);
        $this->authorize('update', $block);

        $section->update([
            'name' => $request->name,
            //'school_id' => $request->school_id,
            'description' => $request->description,
            'slug' => Str::slug($request->name).'-'.uniqid()
        ]);

        return $this->sendResponse($section, "section updated succefully");
    }

     /**
     * @OA\Delete(
     *     path="/api/v1/blocks/{id}",
     *     tags={"Blocks"},
     *     description="delete block ",
     *     @OA\Parameter(
     *         description="Block ID to delete",
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
        $block = Block::findOrFail($id);
        $this->authorize('update', $block);
        $block->delete();
        return $this->sendResponse($block, "block deleted succefully");
    }
}
