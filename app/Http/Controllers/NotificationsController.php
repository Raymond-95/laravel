<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input, Auth, JWTAuth;
use App\User;
use App\Fcm;
use App\Trip;
use App\Triprequest;

class NotificationsController extends Controller
{
   
   public function storeToken(Request $request){

        $where = ['email' => $request->email];
        $user = User::where($where)->first();

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

    public function sendNotification($id, $requestTitle, $requestMsg)
    {
        $target =  Fcm::where('user_id', '=', $id)->first();

        $title = $requestTitle;
        $message = $requestMsg;
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AIzaSyDtxjf0MagAdbElfJ9bkZaH7DfqsFHTHmw';
        $device_token = $target->token;

        $headers = array(
                    'Authorization:key=' .$server_key,
                    'Content-Type:application/json'
                    );

        $fields = array
        (
            'to'  => $device_token,
            'notification' => array('body'=>$message, 'title'=>$title)
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

    public function sendTripRequest(Request $request){

        $trip =  Trip::where('id', '=', $request->id)->first();
        $sender = User::find(Auth::user()->id);
        $recipient =  User::where('id', '=', $trip->user_id)->first();

        $message = 'Hi, I would like to take a ride with you.';

        $this->sendNotification($recipient->id, $sender->name, $message);

        $triprequest = new Triprequest;

        $triprequest->trip_id = $trip->id;
        $triprequest->user_id = $trip->user_id;
        $triprequest->requested_by = $sender->id;

        $triprequest->save();
    }

    public function getNotifications(){

        $user = User::find(Auth::user()->id);

        $where = ['user_id' => $user->id];
        $triprequests = Triprequest::where($where)->get();

        $response = [];

        foreach ($triprequests as $triprequest) {

            $user = User::find($triprequest->requested_by);

            $response[] = [

                'id' => $triprequest->id,
                'requested_by' => $triprequest->requested_by,
                'trip_id' => $triprequest->trip_id,
                'name' => $user->name,
                'imageUrl' => $user->imageUrl,
                'status' => $triprequest->status,
                'source' => $triprequest->trip->source,
                'destination' => $triprequest->trip->destination
            ];
        }

        return response()->api($response);
    }
}

