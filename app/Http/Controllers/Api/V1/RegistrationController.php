<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\ApiResponser;
use App\Models\Invitation;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\{ User, Account, Classroom };
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    use ApiResponser;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'token' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/|min:9'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invitation = Invitation::where('invitation_token', $request->token)->first();
        if(!$invitation) return $this->error('token invalide', 422);

        $current = Carbon::now();
        $expiredAt = ($invitation->created_at)->addHours(24);

        if($current->gt($expiredAt)) return $this->error('token expiré', 422);
        
        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'surname' => $request->surname,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => $current
            ]);

            $role = Role::findOrFail($invitation->role_id);
            $user->assignRole($role->name);
            
            $host_user = User::findOrFail($invitation->user_id);
            $user->accounts()->attach($host_user->accounts[0]->id); 

            if($invitation->classroom_id) {
                //return $invitation;
                $classroom = Classroom::find($invitation->classroom_id);
                //return $classroom;
                $classroom->update(['user_id' => $user->id]);
            }

            $invitation->delete();
            Auth::login($user);
            DB::commit();

            return response()->noContent();

        }catch(\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
