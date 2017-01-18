<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// public function users()
 //    {
 //        return $this->belongsToMany('App\Users', 'Triprequest', 
 //          'trip_id', 'user_id');
 //    }
}
