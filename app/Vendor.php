<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Vendor extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vendors';

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
    protected $fillable = ['last_name', 'first_name', 'middle_name', 'business_name', 'business_address', 'city', 'country', 'tin', 'branch_code', 'opening_balance', 'as_of', 'phone_number', 'fax', 'email', 'individual', 'company_name', 'vendors_status'];

    public static function findOneByName( $name ) 
    {
        $vendor = self::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$name%")
            ->first();

        if(!$vendor) {
            $vendor = self::select('*', 'company_name as name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$name%")
                ->first();
        }

        return $vendor? $vendor: [];
    }

    public function fixed_name() 
    {
        if(isset($this->individual)) {
            if($this->individual) {
                $names = [];
                foreach(['first_name', 'middle_name', 'last_name'] as $field) {
                    if(trim($this->{ $field }))
                        $names[] = $this->{ $field };
                }
                return implode(' ', $names);
            }
            else {
                return $this->company_name;
            }
        }

        return '';
    }
}
