<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    public function trip()
	{
		return $this->belongsTo(Trip::class);
	}
}
