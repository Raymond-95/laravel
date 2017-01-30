<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Chat;
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
        $messages = Chat::where($where)->orWhere($orWhere)->get();

        $response = [];
        foreach ($messages as $message) {

            $response[] = [

                'id' => $message->id,
                'sender' => $message->sender,
                'recipient' => $message->recipient,
                'message' => $message->message
            ];
        }

        return response()
            ->api($response);
    }
}
