<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubAccountType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_account_types';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['account_type_id', 'name', 'description'];
    
    public function account_type()
    {
        return $this->belongsTo('App\ChartAccountType', 'account_type_id');
    }
}
