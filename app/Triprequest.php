<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Triprequest extends Model
{
    public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function trip()
	{
		return $this->belongsTo(Trip::class);
	}
}
