<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            'room_id' => $this->id,
            'room_name' => $this->name,
            'creator_id' => (string) $this->creator_id,
            'created_at' => (string) $this->created_at,
            'users' => UserResource::collection($this->users),
        ];

        $controllerActionParts = explode('@', Route::currentRouteAction());
        $action = $controllerActionParts[1] ?? null;

        if ($action === 'store') {
            unset($result['users']);
        }

        return $result;
    }
}
