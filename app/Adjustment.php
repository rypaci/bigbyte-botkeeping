<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'adjustments';

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
    protected $fillable = ['entity_id', 'entity_type', 'adj_number', 'date', 'amount', 'description'];
    
    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'entity_id');
        
        if( !isset( $this->entity_typea ) )
            return null;
        
        if( $this->entity_type != 'vendor' )
            return null;
        
        return Vendor::where( 'id', $this->entity_id )->first();
    }
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'entity_id');
        $cus = $this->belongsTo('App\Customer', 'customer_id');
        dd( $cus );

        return $cus;
    }
    
    public static function moduleAlias() {
        return 'ad';
    }

}
