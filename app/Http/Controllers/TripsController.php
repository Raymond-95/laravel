<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Trip;
use App\User;
use Auth;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $trip->destination = $request->destination;

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
    public function getDriveTrips()
    {
        $where = ['status' => 'available', 'role' => 'driver'];
        $trips = Trip::where($where)->get();

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

    public function getDriver(Request $request)
    {
        $trip = Trip::find($request->id);

            $response = [

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
        

        return response()
            ->api($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
