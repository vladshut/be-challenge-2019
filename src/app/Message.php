<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed creator_id
 * @property mixed message
 * @property mixed room_id
 * @property User creator
 * @method static create(array $array)
 */
class Message extends Model
{
    protected $fillable = ['message', 'creator_id', 'room_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return BelongsTo|User
     */
    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}
