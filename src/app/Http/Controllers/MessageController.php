<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return MessageResource
     */
    public function store(Request $request)
    {
        $message = Message::create(
            [
                'creator_id' => $request->user_id,
                'room_id' => $request->room_id,
                'message' => $request->message,
            ]
        );

        event(new \App\Events\Message($message));

        return response()->json(['result' => 'message successfully sent']);
    }
}
