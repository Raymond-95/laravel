<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input, Auth;
use App\User;
use App\Friend;

class FriendsController extends Controller
{

    public function addFriend(Request $request){

        $user = User::find(Auth::user()->id);
        $newfriend = new Friend;

        $newfriend->user_id = $user->id;
        $newfriend->friend_id = Input::get('friend_id');

        $newfriend->save();

        return response()->api(['message' => 'Friend request is sent']);
    }

    public function getVerifiedUser(Request $request){

        $user = User::find(Auth::user()->id);
        $friends = Friend::all();
        $verify = 'true';

        $userId = $user->id;
        $friendId = $request->friend_id;

        if (!empty($friends)){

            foreach ($friends as $friend) {

                if ($friend->user_id == $userId || $friend->friend_id == $userId){

                    if ($friend->user_id == $friendId || $friend->friend_id == $friendId){

                        if ($friend->status == 'pending' || $friend->status == 'cancelled'){
                            $verify = 'false';
                        }
                    }
                }
            }
        }
        
         return response()->api($verify);
    }
    

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
