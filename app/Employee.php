<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $fillable = [
        'employee_name',
        'tin_no',
        'address',
        'birthday',
        'sex',
        'status',
        'dependents',
        'salary_method',
        'daily_rate',
        'monthly_rate',
        'overtime_rate',
        'absent_rate',
        'late_rate',
        'employee_status',
        'min_hours_per_day'
    ];
}