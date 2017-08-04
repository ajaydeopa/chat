<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelUser extends Model
{
    //
	protected $fillable = [ 'user_id', 'channel_id' ];
	public $timestamps = false;
}
