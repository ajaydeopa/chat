<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Songs extends Model
{
    //
	protected $fillable = [ 'name', 'duration' ];
	
	public $timestamps = false;

}
