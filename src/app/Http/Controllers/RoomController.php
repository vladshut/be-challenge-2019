<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoomResource::collection(Room::all());
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RoomResource
     */
    public function store(Request $request)
    {
        $room = Room::create([
            'creator_id' => $request->user_id,
            'name' => $request->name,
        ]);

        return new RoomResource($room);
    }

    /**
     * Display the specified resource.
     *
     * @param Room $room
     * @return RoomResource
     */
    public function show(Room $room)
    {
        return new RoomResource($room);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Room $room
     * @return Response
     * @throws \Exception
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json(['result' => 'room removed successfully']);
    }

    /**
     * Join the room.
     *
     * @param Room $room
     * @param Request $request
     * @return Response
     */
    public function join(Room $room, Request $request)
    {
        $room->users()->attach($request->user_id);

        return response()->json(['result' => 'user successfully joined the room']);
    }

    /**
     * Leave the room.
     *
     * @param Room $room
     * @param Request $request
     * @return Response
     */
    public function leave(Room $room, Request $request)
    {
        $room->users()->detach($request->user_id);

        return response()->json(['result' => 'user successfully left the room']);
    }
}
