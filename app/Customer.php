<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Customer extends Model
{
    // protected $table = 'customers';
    
    protected $primaryKey = 'id';
    
    public $fillable = ['individual','last_name','first_name','middle_name','company_name','business_name','business_address','city','country','tin','branch_code','phone_no','fax','email'];

    public static function findOneByName( $name ) 
    {
        $customer = self::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$name%")
            ->first();

        if(!$customer) {
            $customer = self::select('*', 'company_name as name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$name%")
                ->first();
        }

        return $customer? $customer: [];
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