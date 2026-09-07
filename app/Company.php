<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

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
    protected $fillable = ['last_name', 'first_name', 'middle_name', 'gender', 'civil_status', 'spouse', 'business_name', 'business_id', 'business_address', 'city', 'country', 'zip', 'business_type', 'tin', 'branch_code', 'rdo_code', 'phone_number', 'fax', 'email', 'individual', 'company_name', 'registration_date', 'registration_number'];

    public static function blankInfo()
    {
        $company_fields = (new self)->getFillable();

        $company = (object) [];
        foreach($company_fields as $f) {
            if($f == 'individual')
                $company->{ $f } = 1;
            else
                $company->{ $f } = '';
        }
        
        if( !isset($company->id) ) $company->id = 0;

        return $company;
    }

    public static function info()
    {
        $company = self::first();

        if( !$company ) {
            $company = self::blankInfo();
        }

        return $company;
    }
    
    public static function getCompanyNameFromInfo( $company )
    {
        $name = $company->business_name;
        if( !$name )
            $name = "Your Company Name";

        return $name;
    }
}
