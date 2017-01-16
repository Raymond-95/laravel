<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon;

class Trip extends Model
{

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
