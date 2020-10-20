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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('chat.chat_page');
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

    public function private_chat($receiver_id , Request $request)
    {
        $messages = Message::where(function ($query) use ($receiver_id){
            $query->where('receiver_id',$receiver_id)->Where('sender_id',auth()->id());
        })->orWhere(function ($query) use ($receiver_id){
            $query->where('sender_id',$receiver_id)->Where('receiver_id',auth()->id());
        })
            ->orderBy('created_at','desc')->with('sender',
                'receiver')->paginate(10);
        return response()->json([
            'messages' => $messages
        ]);
    }

    public function send_private_message($receiver_id,Request $request)
    {
        $message = Message::create([
            "content"     => $request->input('content'),
            "sender_id"   => auth()->id(),
            "receiver_id" => $receiver_id,
        ]);
        $message->load('sender');
        broadcast(new PrivateMessageSent($message));
        return $message;
    }
}
