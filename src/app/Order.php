<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property bool|DateTime datetime
 */
class Order extends Model
{
    protected $fillable = ['user_id', 'room_id', 'datetime', 'type', 'remind_in'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
