<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function triprequest()
    {
        return $this->hasMany(Triprequest::class);
    }

	public function guardian()
    {
        return $this->hasMany(Guardian::class);
    }
}
