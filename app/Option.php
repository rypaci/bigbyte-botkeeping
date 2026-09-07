<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    
    protected $table = 'options';
    
    protected $primaryKey = 'id';
    
    protected $fillable = ['name', 'value'];
    
    public $prefix = '';
    public $accountFields = [];
    
    public function getCoas($option_name = null)
    {
        if(!$option_name)
            return [];
        
        $option = $this->where('name', "{$this->prefix}$option_name")->first();
        
        $coas = ($option && $option->value)? $option->value: [];
        
        if( gettype($coas) == 'string' ) 
            $coas = explode(',', $coas);
        
        return $coas;
    }
    
    public function getAllCoas()
    {
        $accounts = new \stdClass;
        
        foreach($this->accountFields as $field) {
            $accounts->{ $field } = $this->getCoas($field);
        }
        
        return $accounts;
    }
    
    public function setCoa($option_name, $option_value)
    {
        $option = $this->firstOrNew(['name' => "{$this->prefix}$option_name"]);
        $option->value = implode(',', $option_value);
        $option->save();
    }
}
