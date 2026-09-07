<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DtrPassword extends Model
{
    public $fillable = ['e_id','password','email','biometrics','username','token'];
}
