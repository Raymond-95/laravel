<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input, Auth, JWTAuth;
use App\User;
use App\Fcm;

class NotificationsController extends Controller
{
   
   public function storeToken(){

        $user = User::find(Auth::user()->id);

        $newFcm = new Fcm;
        $newFcm->user_id = $user->id;
        $newFcm->token = trim(Input::get('token')); 
        $newFcm->save();

        return response()->api(['message' => 'Register token successfully']);
   }

   public function updateToken()
    {
        $user = User::find(Auth::user()->id);

        $Fcm =  Fcm::where('user_id', '=', $user->id)->first();
        $Fcm->token = trim(Input::get('token')); 
        $trip->save();

        return response()->api(['message' => 'Update token successfully']);
    }

    public function sendNotification(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $target =  Fcm::where('user_id', '=', $request->user_id)->first();

        $title = 'Tee Yong Ching';
        $message = 'I am a big head';
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AIzaSyDtxjf0MagAdbElfJ9bkZaH7DfqsFHTHmw';
        $device_token = $target->token;

        $headers = array(
                    'Authorization:key=' .$server_key,
                    'Content-Type:application/json'
                    );

        $msg = array
        (
            'title'     => $title,
            'body'   => $message
        );

        $fields = array
        (
            'to'  => $device_token,
            'notification' => $msg
        );

        $payload = json_encode($fields);

        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($curl_session);

        curl_close($curl_session);
    }
}

