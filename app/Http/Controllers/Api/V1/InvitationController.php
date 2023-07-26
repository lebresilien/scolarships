<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:invitations'],
            'selectedRoleValue' => ['required'],
            'classroom_id' => ['nullable', 'exists:classrooms,id']
        ]);

        $role = DB::table('roles')->where('name', $request->selectedRoleValue)->first();
       
        $invitation = new Invitation($request->except('selectedRoleValue'));
        $invitation->generateInvitationToken();
        $invitation->user_id = $request->user()->id;
        $invitation->role_id = $role->id;

       // if(!empty($request->classroom_id)) $invitation->classroom_id = $request->classroom_id;
        
        $invitation->save();

        $url = config('app.frontend_url').'/registration?token='.$invitation->invitation_token;

        Mail::to($request->get('email'))->send(new InvitationMail($invitation, $url));
        
        return response()->noContent();
    }
}
