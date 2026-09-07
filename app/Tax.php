<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'taxes';

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
    protected $fillable = ['name', 'type', 'description', 'rate', 'chart_account_id'];
    
    public function chart_account()
    {
        return $this->belongsTo('App\ChartAccount', 'chart_account_id');
    }
    
    public static function getAllWithholdingTaxes() {
        $taxes = self::from('taxes as t')->select('t.id', 't.name', 't.rate', 't.chart_account_id', 'ca.name as chart_account_name', 'ca.code as code')
            ->leftJoin('chart_accounts as ca', 'ca.id', '=', 't.chart_account_id')
            ->where('type', 'LIKE', '%withholding%')
            ->where('chart_account_id', '<>', '0')
            ->orderBy('name', 'ASC')
            ->get();
        
        if(!$taxes) $taxes = [];
        else $taxes = $taxes->toArray();
        
        $default = [ 'id' => '0', 'name' => 'Select Withholding tax', 'rate' => '0.00' ];
        array_unshift($taxes, $default);
        
        $taxes_by_id = [];
        foreach($taxes as $tax) {
            $taxes_by_id[ $tax['id'] ] = (object) $tax;
        }
        
        return $taxes_by_id;
    }
    
    public static function getVat()
    {
        $vat = self::find(19);
        $vat->ca_id   = (isset($vat->chart_account)) ? $vat->chart_account->id : 0;
        $vat->ca_name = (isset($vat->chart_account)) ? $vat->chart_account->name : '';
        $vat->ca_code = (isset($vat->chart_account)) ? $vat->chart_account->code : '';
        
        return $vat;
    }
}
