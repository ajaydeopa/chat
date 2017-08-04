<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
	protected $fillable = [ 'user_id', 'channel_id', 'message', 'at' ];
	public $timestamps = false;
}
