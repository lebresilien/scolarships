<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Models\{School, User};
use Illuminate\Support\Str;

class SchoolController extends BaseController
{
    
    /**
     * @OA\Post(
     *     path="/api/v1/schools",
     *     tags={"School"},
     *     summary="Creating new School",
     *     description="ECreating new School",
     *     @OA\RequestBody(
     *         description="School Form create",
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
     *                      property="immatriculation",
     *                      description="Immatriculation.",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="Description",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="devise_fr",
     *                      description="devise en Français",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="devise_en",
     *                      description="Devise en Anglais.",
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
                'name' => ['required', 'string'],
                'immatriculation' => ['required', 'string'],
                'devise_fr' => ['required', 'string'],
                'devise_en' => ['required', 'string'],
            ]
        )->validate();

        $school = School::create(['user_id' => auth('api')->user()->id, 'name' => $request->name,'devise_fr' => $request->devise_fr,'devise_en' => $request->devise_en,
        "immatriculation" => $request->immatriculation,"description" => $request->description, "slug" => Str::slug($request->name).'-'.uniqid() ]);

        return $this->sendResponse($school, "school created succefully");
    }

    /**
     * @OA\get(
     *     path="/api/v1/school/{id}",
     *     tags={"School"},
     *     summary="Details school",
     *     description="Show details school",
     *     @OA\Parameter(
     *          name="id",
     *          description="School ID identification",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
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
    public function show($id)
    {
        /* $school = School::findOrFail($id);
        return $this->sendResponse($school, "school details");  */
    }

     /**
     * @OA\put(
     *     path="/api/v1/schools/{id}",
     *     tags={"School"},
     *     summary="Updating school information",
     *     description="Upadting school information",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID school identification",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
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
     *                      property="immatriculation",
     *                      description="authorization id",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="devise_fr",
     *                      description="Devise en Français",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="devise_en",
     *                      description="Devise en Anglais",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="School description",
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
    public function update(Request $request, $id)
    {
        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string'],
                'immatriculation' => ['required', 'string'],
                'devise_fr' => ['required', 'string'],
                'devise_en' => ['required', 'string'],
            ]
        )->validate();

        $school = School::findOrFail($id);
        if(!$school) return ;

        $school->update(['name' => $name,'devise_fr' => $request->devise_fr,'devise_en' => $request->devise_en,
        "immatriculation" => $request->immatriculation,"description" => $request->description, "slug" => Str::slug($request->name).'-'.uniqid() ]);

        return $this->sendResponse($school, "school updated succefully");
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/school/{id}",
     *     tags={"School"},
     *     description="delete school ",
     *     @OA\Parameter(
     *         description="School ID to delete",
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
        $school = School::findOrFail($id);
        if(!$school) return;
        $school->delete();
        return $this->sendResponse($school, "school updated succefully");
    }

     /**
     * return user schools list 
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *   path="/api/v1/schools/users",
     *   tags={"School"},
     *  
     *   @OA\Response(
     *     response="200",
     *     description="return School collection"
     *   ),
     *   @OA\Response(
     *      response=401,
     *      description="Unauthenticated",
     *      ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden")
     * )
     */
    public function userSchool()
    {
        $schools = School::where('user_id', auth('api')->user()->id)->get();
        return $this->sendResponse($schools, "User school list");
    }

    public function tests()
    {
        $data = [22,13];
        $faker = \Faker\Factory::create();
        return $this->sendResponse($data, 'helloworld');
      // return response()->json(['message' => 'hello'],200);
    }
}
