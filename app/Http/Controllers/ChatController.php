<?php

namespace App\Http\Controllers;

use App\Events\GeneralMessageSent;
use App\Events\PrivateMessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function general(Request $request)
    {
        $messages = Message::whereNull("receiver_id")->orderBy('created_at','desc')->with('sender',
            'receiver')->paginate(10);
        return response()->json([
            'messages' => $messages
        ]);
    }

    public function send_general_message(Request $request)
    {
        $message = Message::create([
            "content"     => $request->input('content'),
            "sender_id"   => auth()->id(),
            "receiver_id" => null,
        ]);
        $message->load('sender');
        broadcast(new GeneralMessageSent($message))->toOthers();
        return $message;
    }


}
