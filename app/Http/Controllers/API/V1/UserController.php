<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\SignUserRequest;
use Illuminate\Support\Facades\DB;
use App\Models\{ User, UserClasse };

class UserController extends Controller
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
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Creating new User",
     *     description="Creating new User",
     *     @OA\RequestBody(
     *         description="User Form create",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="lname",
     *                      description="Prenom.",
     *                      type="string",
     *                      required=false
     *                  ),
     *                  @OA\Property(
     *                      property="fname",
     *                      description="Nom.",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      description="email.",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password.",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      description="Phone number.",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="cni",
     *                      description="ID card.",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="situation",
     *                      description="Situation",
     *                      type="string",
     *                      required=false
     *                  ),
     *                  @OA\Property(
     *                      property="role_id",
     *                      description="User role",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="classe_id",
     *                      description="User clase",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="school_id",
     *                      description="User school",
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
    public function store(SignUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        try{

            DB::beginTransaction();

            if(!$user)
            {
                $usr = User::create([
                    'lname' => $request->lname,
                    'fname' => $request->fname,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'cni' => $request->cni,
                    'situation' => $request->situation,
                    'password' => $request->password,
                ]);

                $usr->assignRole($request->role_id);

                $user_classe = UserClasse::create([
                    'user_id' => $request->usr_id,
                    'classe_id' => $request->classe_id,
                    'school_id' => $request->school_id,
                ]);

                $user = $usr; 

            }
            else
            {
                $user->assignRole($request->role_id);
                $user_classe = UserClasse::create([
                    'user_id' => $request->user_id,
                    'classe_id' => $request->classe_id,
                    'school_id' => $request->school_id,
                ]);
            }

            DB::commit();
            return $this->sendResponse($user, 'operation succefully');

        } catch (\PDOException $e) {
            return $e->getMessage();
            DB::rollBack();
        }
    }

    /**
     * @OA\get(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Details user",
     *     description="Show details user",
     *     @OA\Parameter(
     *          name="id",
     *          description="User ID identification",
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
         $user = User::findOrFail($id);
         return $this->sendResponse($user, 'users details');
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
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     description="delete user ",
     *     @OA\Parameter(
     *         description="User ID to delete",
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
        if(auth('api')->user()->hasPermissionTo('delete user'))
        {
            $user = User::findOrFail($id);
            $school = School::findOrFail(auth('api')->user()->id);
            $user_classe = UserClasse::where('user_id', $id)
                                        ->where('school_id', $school->id)
                                        ->first();

            $user_classe->delete();
            $user->delete();
            return $this->sendResponse($user, "section deleted succefully");
        }
    }

    public function invite()
    {
      if(auth('api')->user()->hasPermissionTo('invite user'))
      {
        
      }
    }
}
