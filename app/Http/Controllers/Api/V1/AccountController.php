<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ User, Account };
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{

    use ApiResponser;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = Account::where('id', $id)
                           ->with('users')
                           ->get();
        $users = $users->pluck('users');
        $data;
        foreach($users as $user) {
            $data = $user;
        }
        return $this->success($data);
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

    public function signature_pad(Request $request) {
        
        $folderPath = "public/signatures/";

        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory('/public/signatures', 0777, true, true);
        }
  
        $img = $request->signature_base64;

        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
       
        $image_base64 = base64_decode($image_parts[1]);
        $file_name = uniqid() . '.'.$image_type;
        $file = $folderPath . $file_name;

        $account = Account::findOrFail($request->user()->accounts[0]->id);
        $account->signature_base64 = $file_name;
        $account->save();

        Storage::put($file, $image_base64);

        return response()->noContent();
    }
}
