<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Chat;
use App\User;
use Auth;

class ChatsController extends Controller
{

    public function storeMessage(Request $request){
        $user_id = Auth::user()->id;
        $chat = new Chat;

        $id = $request->id;

        $chat->sender = $user_id;
        $chat->recipient = $id;
        $chat->message = $request->message;

        $chat->save();
    }

    public function getMessage(Request $request){

        $user_id = Auth::user()->id;

        $where = ['sender' => $user_id, 'recipient' => $request->id];
        $orWhere = ['sender' => $request->id, 'recipient' => $user_id];
        $chats = Chat::where($where)->orWhere($orWhere)->get();

        $response = [];
        foreach ($chats as $chat) {

            $response[] = [

                'id' => $chat->id,
                'sender' => $chat->sender,
                'recipient' => $chat->recipient,
                'message' => $chat->message
            ];
        }

        return response()
            ->api($response);
    }

    public function getChatUsers(){
        $user_id = Auth::user()->id;

        $where = ['sender' => $user_id];
        $orWhere = ['recipient' => $user_id];
        $chats = Chat::where($where)->orWhere($orWhere)->get();

        $temp_id = [];
        foreach ($chats as $chat){
            if ($chat->sender != $user_id){
                $temp_id[] = [
                        'id' => $chat->sender,
                        'sender' => $chat->sender,
                        'message' => $chat->message
                    ];
            }
            else{
                $temp_id[] = [
                        'id' => $chat->recipient,
                        'sender' => $chat->sender,
                        'message' => $chat->message
                    ];
            }
        }

        $temp_id = array_reverse($temp_id);

        $array = array_column($temp_id, 'id');
        $array = array_unique($array);
        $ids = array_filter($temp_id, function ($key, $value) use ($array) {
            return in_array($value, array_keys($array));
        }, ARRAY_FILTER_USE_BOTH);

        $response = [];

        foreach ($ids as $id){
            
            $user =  User::find($id['id']);

            $response[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'imageUrl' => $user->imageUrl,
                        'sender' => $id['sender'],
                        'message' => $id['message']
                    ];
        }
        return response()->api($response);
    }
}
