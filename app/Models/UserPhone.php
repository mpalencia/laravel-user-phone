<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{

    /**
    * Table name on DB
    */
    protected $table = 'user_phones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'phone_number'
    ];


}
