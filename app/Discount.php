<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $fillable = ['discount', 'rate', 'chart_account_id'];
    
    public function chart_account()
    {
        return $this->belongsTo('App\ChartAccount', 'chart_account_id');
    }
    
    public static function getScPwd()
    {
        $discount = self::find(1);
        $discount->ca_id   = (isset($discount->chart_account)) ? $discount->chart_account->id : 0;
        $discount->ca_name = (isset($discount->chart_account)) ? $discount->chart_account->name : '';
        $discount->ca_code = (isset($discount->chart_account)) ? $discount->chart_account->code : '';
        
        return $discount;
    }
    
}