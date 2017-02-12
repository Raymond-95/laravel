<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Trip;

class SearchController extends Controller
{
    public function getCoordinate($location){
        $address = $location; // Google HQ
        $prepAddr = str_replace(' ','+',$address);
        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
        $output= json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        return [$latitude, $longitude];  
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
        $duration = $response_a['rows'][0]['elements'][0]['duration']['value'];

        return [$dist, $duration];
    }

    public function search(Request $request){

        $where = ['role' => $request->role, 'status' => 'available'];
        $trips = Trip::where($where)->orderBy('date')->get();

        $geocoordinate = $this->getCoordinate($request->source);
        $searched_source_lat = $geocoordinate[0];
        $searched_source_lng = $geocoordinate[1];

        $geocoordinate = $this->getCoordinate($request->destination);
        $searched_destination_lat = $geocoordinate[0];
        $searched_destination_lng = $geocoordinate[1];

        $newTrips = [];
        foreach ($trips as $trip) {

            $result = $this->GetDrivingDistance($trip->source_lat, $trip->source_lng, $trip->destination_lat, $trip->destination_lng);

            $source_to_source = $this->GetDrivingDistance($trip->source_lat, $trip->source_lng, $searched_source_lat, $searched_source_lng);

            $des_to_des = $this->GetDrivingDistance($searched_destination_lat, $searched_destination_lng, $trip->destination_lat, $trip->destination_lng);

            $search_trip = $this->GetDrivingDistance($searched_source_lat, $searched_source_lng, $searched_destination_lat, $searched_destination_lng);

            $difference =  ($source_to_source[0] + $des_to_des[0] + $search_trip[0] - $result[0]) / 1000; 
            $extra = ($source_to_source[0] + $des_to_des[0]) / 1000;
            $minutes = floor(($source_to_source[1] + $des_to_des[1] + $search_trip[1] - $result[1]) / 60);
            $seconds = ($source_to_source[1] + $des_to_des[1] + $search_trip[1] - $result[1]) % 60;
            $duration = $minutes.'m'.$seconds.'s';

            if ($difference <= 10 && $extra <=10){
                $newTrips[] = [
                    'id' => $trip->id,
                    'distance' => $difference,
                    'duration' => $duration
                ];
            }
        }

        $response = [];
        if (!empty($newTrips)){
            
            foreach ($newTrips as $newTrip) {

                $where = ['id' => $newTrip['id']];
                $trip = Trip::where($where)->orderBy('date')->first();

                $response[] = [

                    'id' => $trip->id,
                    'source' => $trip->source,
                    'destination' => $trip->destination,
                    'date'  => $trip->date,
                    'name' => $trip->user->name,
                    'imageUrl' => $trip->user->imageUrl,
                    'status' => $trip->status,
                    'distance' => $newTrip['distance'],
                    'duration' => $newTrip['duration'],
                ];
            }
        }

            return response()
                ->api($response);
    }
}
