<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChartAccountType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chart_account_types';

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
    protected $fillable = ['min', 'max', 'name'];
    
    public function sub_account_types()
    {
        return $this->hasMany('App\SubAccountType', 'account_type_id');
    }
}
