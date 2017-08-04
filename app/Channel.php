<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    //
	protected $fillable = [ 'channel', 'type' ];
	
	public $timestamps = false;

	public function user()
  {
      return $this->hasMany('App\ChannelUser');
  }

  public function message()
  {
      return $this->hasMany('App\Message');
  }
}
