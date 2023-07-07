<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Spatie\Permission\Models\Role;

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
            'role_id' => ['required', 'exists:roles,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id']
        ]);

        $invitation = new Invitation($request->all());
        $invitation->generateInvitationToken();
        $invitation->user_id =  $request->user()->id;

       // if(!empty($request->classroom_id)) $invitation->classroom_id = $request->classroom_id;
        
        $invitation->save();

        $url = config('app.frontend_url').'/registration?token='.$invitation->invitation_token;

        Mail::to($request->get('email'))->send(new InvitationMail($invitation, $url));
        
        return response()->noContent();
    }
}
