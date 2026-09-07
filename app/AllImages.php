<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllImages extends Model
{
    protected $table = 'all_images';
    
    public $fillable = ['image_name', 'foreign_id', 'keys', 'date'];
}
