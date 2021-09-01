<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInvitationRequest;
use App\Models\{ Invitation };
use Spatie\Permission\Models\Role;
use App\Notifications\InvitationNotification;
use App\Mail\InviteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InvitationController extends Controller
{
    
    public function create()
    {
       $roles = Role::all();
       $classes = Classes::all();
    }

    public function store(StoreInvitationRequest  $request)
    {
        $invitation = new Invitation($request->all());
        $invitation->generateInvitationToken();
        $invite = $invitation->save();

        $url = URL::temporarySignedRoute(
 
            'registration', now()->addMinutes(3),
                [
                    'token' => $invitation->invitation_token,
                    'role_id' => $invitation->role_id,
                    'classe_id' => $invitation->classe_id
                ]
        );

        Mail::to($request->get('email'))->send(new InviteMail($invitation, $url));
        return  response()->json(['message' => 'Invitation send to user']);
    }

    public function registration(Request $request)
    {
        
        echo 'djd';
    }

    public function create(Request $request)
    {
        
    }
}
