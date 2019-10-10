<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return a list of names with default ordering
     *
     * @return void
     */
    public static function ordered()
    {
        // order names by last, then first name
        return self::orderBy('last_name')
            ->orderBy('first_name');
    }
}
