<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Trip;
use App\User;
use App\Triprequest;
use Auth, View, Redirect, Input;

class TripsController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $trip = new Trip;

        $trip->source = $request->source;
        $geocoordinate = $this->getCoordinate($request->source);
        $trip->source_lat = $geocoordinate[0];
        $trip->source_lng = $geocoordinate[1];

        $trip->destination = $request->destination;
        $geocoordinate = $this->getCoordinate($request->destination);
        $trip->destination_lat = $geocoordinate[0];
        $trip->destination_lng = $geocoordinate[1];

        $date = strtotime($request->date);
        $trip->date = date('Y-m-d',$date);

        $time = strtotime($request->time);
        $trip->time = date('H:i', $time);

        $trip->role = $request->role;
        $trip->information = $request->information;
        $trip->user_id = $user->id;

       $trip->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTrips(Request $request)
    {
        $where = ['status' => 'available', 'role' => $request->role];
        $trips = Trip::where($where)->orderBy('date')->get();

        $response = [];
        foreach ($trips as $trip) {

            $response[] = [

                'id' => $trip->id,
                'source' => $trip->source,
                'destination' => $trip->destination,
                'date'  => $trip->date,
                'time'  => $trip->time,
                'information'  => $trip->information,
                'role' => $trip->role,
                'user_id' => $trip->user_id,
                'name' => $trip->user->name,
                'imageUrl' => $trip->user->imageUrl
            ];
        }

        return response()
            ->api($response);
    }

    public function getTripDetails(Request $request)
    {
        $trip = Trip::find($request->id);

        $where = ['trip_id' => $request->id, 'requested_by' => Auth::user()->id] ;
        $requested_trip = Triprequest::where($where)->first();

        if (!empty($requested_trip)){
            $status = $requested_trip->status;
        }
        else{
            $status = $trip->status;
        }

            $response = [

                'id' => $trip->id,
                'source' => $trip->source,
                'destination' => $trip->destination,
                'date'  => $trip->date,
                'time'  => $trip->time,
                'information'  => $trip->information,
                'role' => $trip->role,
                'status' => $status,
                'user_id' => $trip->user_id,
                'name' => $trip->user->name,
                'imageUrl' => $trip->user->imageUrl
            ];
        

        return response()
            ->api($response);
    }

    public function getHistory()
    {
        $user_id = Auth::user()->id;

        $where = ['requested_by' => $user_id];
        $requested_trips = Triprequest::where($where)->get();

        $tripId = array();
        foreach ($requested_trips as $requested_trip) {

           array_push($tripId, $requested_trip->trip_id);
        }

        $trips = Trip::where('user_id', '=', $user_id)->get();

        foreach ($trips as $trip) {

            array_push($tripId, $trip->id);
        }

        $response = [];
        $size = count($tripId);
        for ($i = 0; $i < $size; $i++) {

            $temp_trip = Trip::where('id', '=', $tripId[$i])->get();

            foreach ($temp_trip as $trip) {

                $response[] = [

                    'id' => $trip->id,
                    'source' => $trip->source,
                    'destination' => $trip->destination,
                    'date'  => $trip->date,
                    'time'  => $trip->time,
                    'information'  => $trip->information,
                    'role' => $trip->role,
                    'user_id' => $trip->user_id,
                    'name' => $trip->user->name,
                    'imageUrl' => $trip->user->imageUrl
                ];
            }
        }

        $response = array_values(array_sort($response, function ($value) {
                return $value['date'];
        }));

        return response()
            ->api($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTrip(Request $request, $id)
    {
        $trip =  Trip::where('id', '=', $id)->first();

        $trip->source = $request->source;
        $trip->destination = $request->destination;

        $date = strtotime($request->date);
        $trip->date = date('Y-m-d',$date);

        $time = strtotime($request->time);
        $trip->time = date('H:i', $time);

        $trip->role = $request->role;
        $trip->information = $request->information;

        $trip->save();

            return response()
            ->api("Trip is updated.");
    }

    public function updateTripRequest(Request $request, $id)
    {
        $triprequest =  Triprequest::where('id', '=', $id)->first();
        $user_id = Auth::user()->id;

        $triprequest->trip_id = $request->trip_id;
        $triprequest->user_id = $user_id;
        $triprequest->requested_by = $request->requested_by;
        $triprequest->status = $request->status;
        $triprequest->save();


        $trip = Trip::where('id', $request->trip_id)->first();

        $trip->source = $trip->source;
        $trip->destination = $trip->destination;
        $trip->date =  $trip->date;
        $trip->time = $trip->time;
        $trip->role = $trip->role;
        $trip->information = $trip->information;
        $trip->status = $request->trip_status;
        $trip->save();

        return response()
        ->api("Trip request is updated.");
    }
}
