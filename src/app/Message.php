<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed creator_id
 * @property mixed message
 * @property mixed room_id
 * @method static create(array $array)
 */
class Message extends Model
{
    protected $fillable = ['message', 'creator_id', 'room_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}
