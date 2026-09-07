<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DTRabsent extends Model
{
    public $fillable = ['e_id','date','absent_no','user_account_id','remarks'];
}
