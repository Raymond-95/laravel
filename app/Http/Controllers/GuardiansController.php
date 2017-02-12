<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Trip;
use App\User;
use App\Guardian;
use Auth, View, Redirect, Input;

class GuardiansController extends Controller
{
    public function updateGuardian(Request $request, $id)
    {
        $guardian =  Guardian::where('id', '=', $id)->first();
        $user_id = Auth::user()->id;

        $guardian->trip_id = $request->trip_id;
        $guardian->requested_by = $request->guardian;
        $guardian->guardian = $user_id;
        $guardian->status = $request->status;
        $guardian->save();
    }

    public function getGuardians(){
        $user_id = Auth::user()->id;
        $guardians =  Guardian::where('requested_by', '=', $user_id)->get();

        $response = [];

        foreach ($guardians as $guardian){

            $user =  User::find($guardian->guardian);

            $response[] = [
                            'trip_id' => $guardian->trip_id,
                            'name' => $user->name,
                            'imageUrl' => $user->imageUrl
                        ];
        }   

        return response()->api($response);
    }
   
}
