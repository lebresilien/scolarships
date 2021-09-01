<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Academy, User};
use Illuminate\Support\Str;

class AcademyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Academy::all();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academies",
     *     tags={"Academies"},
     *     summary="Creating new Academy",
     *     description="Creating new Academy",
     *     @OA\RequestBody(
     *         description="Academy Form create",
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
     *                      type="interger",
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
                'name' => ['required', 'string'],
                'school_id' => ['required', 'exists:schools,id'],
            ]
        )->validate();

        $academy = Academy::create(['school_id' => $request->school_id, 'name' => $request->name,
        "slug" => Str::slug($request->name).'-'.uniqid() ]);

        return $this->sendResponse($academy, "Academy created succefully");
    }

    /**
     * @OA\get(
     *     path="/api/v1/academies/{slug}",
     *     tags={"Academy"},
     *     summary="Details Academy",
     *     description="Show details academy",
     *     @OA\Parameter(
     *          name="id",
     *          description="School slug identification",
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
        $academy = Academy::where('slug', $slug)->first();
        if(!$academy) return response()->json(['message' => 'Academy not found']);
        return $this->sendResponse($academy, "Academy details");  
    }

     /**
     * @OA\put(
     *     path="/api/v1/academies/{slug}",
     *     tags={"Academies"},
     *     summary="Updating academy information",
     *     description="Upadting academy information",
     *     @OA\Parameter(
     *          name="slug",
     *          description="slug academy identification",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Form school updating",
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
                'name' => ['required', 'string'],
                'school_id' => ['required', 'exists:schools,id'],
            ]
        )->validate();

        $academy = Academy::where('slug', $slug)->first();
        if(!$academy) return response()->json(['message' => 'Academy not found']);

        $school->update(['name' => $request->name,'school_id' => $request->school_id, "slug" => Str::slug($request->name).'-'.uniqid() ]);

        return $this->sendResponse($academy, "academy updated succefully");
    }

       /**
     * @OA\Delete(
     *     path="/api/v1/academies/{id}",
     *     tags={"Academies"},
     *     description="delete academy ",
     *     @OA\Parameter(
     *         description="Academy ID to delete",
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
     *         description="Academy deleted"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error"
     *     )
     * )
     */
    public function destroy($id)
    {
        $academy = Academy::findOrFail($id);
        $academy->delete();
        
        return $this->sendResponse($academy, "academy updated succefully");
    }

}
