<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    public $fillable = ['pay_id','e_id','salary_days_present','salary_rate','salary_total','overtime','overtime_rate','overtime_total','cola','total','emp_con_sss','emp_con_phic','emp_con_pagibig','late','late_rate','late_total','employer_con_sss','employer_con_phic','employer_con_pagibig','net_pay','tax_withheld','days_absent','absent_rate','absent_total','salary_method','holiday','date'];
}