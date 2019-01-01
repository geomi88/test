<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {

    protected $fillable = ['username', 'password','first_name', 'middle_name','last_name',
        'passport_number','father_name','mother_name','mobile_number','email','contact_email','profilepic','dob','current_address',
        'religion_id','gender','nationality','nationality_type','company','residence_id_number','alias_name','division','created_by'];

}
