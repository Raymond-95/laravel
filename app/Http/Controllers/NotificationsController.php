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
use App\Guardian;

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

    public function sendRequest(Request $request){

        $trip =  Trip::where('id', '=', $request->id)->first();
        $sender = User::find(Auth::user()->id);
        $recipient =  User::where('id', '=', $trip->user_id)->first();

        if ($request->type == 'triprequest'){
            $message = 'Hi, I would like to take a ride with you.';

            $this->sendNotification($recipient->id, $sender->name, $message);

            $triprequest = new Triprequest;

            $triprequest->trip_id = $trip->id;
            $triprequest->user_id = $trip->user_id;
            $triprequest->requested_by = $sender->id;

            $triprequest->save();
        }
        else{
            $message = 'My angle, please protect me from the dangers.';

            $this->sendNotification($recipient->id, $sender->name, $message);

            $guardian = new Guardian;

            $guardian->trip_id = $trip->id;
            $guardian->requested_by = $sender->id;
            $guardian->guardian = $recipient->id;

            $guardian->save();
        }
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
                'destination' => $triprequest->trip->destination,
                'type' => 'triprequest',
                'created_at' => $triprequest->created_at
            ];
        }


        $where = ['requested_by' => $user->id];
        $guardians = Guardian::where($where)->get();

        foreach ($guardians as $guardian) {

            $user = User::find($guardian->requested_by);

            $response[] = [

                'id' => $guardian->id,
                'requested_by' => $guardian->requested_by,
                'trip_id' => $guardian->trip_id,
                'name' => $user->name,
                'imageUrl' => $user->imageUrl,
                'status' => $guardian->status,
                'source' => $guardian->trip->source,
                'destination' => $guardian->trip->destination,
                'type' => 'guardian',
                'created_at' => $guardian->created_at
            ];
        }

        collect($response)->sortBy('created_at');

        return response()->api($response);
    }

    public function GetDrivingDistance($lat1, $long1, $lat2, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];

        return $dist/1000;
    }

    public function alertGuardian(Request $request){

        $start_lat = $request->start_lat;
        $start_lng = $request->start_lng;

        $current_lat = $request->current_lat;
        $current_lng = $request->current_lng;

        $distance = $this->GetDrivingDistance($start_lat, $start_lng, $current_lat, $current_lng);

        if ($distance <= 4){

            $user = User::find(Auth::user()->id);
            $message = "Something terrible might happen to me. Please check up on me.";
            $this->sendNotification($request->recipient, $user->name, $message);
        }
    }
}

