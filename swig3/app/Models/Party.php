<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model

{
    protected $table = 'ac_party';
    
    protected $fillable = ['first_name','last_name','alias_name','ledger_group_id','company_id','code','supplier_type','registration_type',
        'supplier_pin','address','contact_person','mobile_number','contact_number','email','contact_email','nationality','party_type',
//        'bank_beneficiary_name','bank_account_number','bank_iban_no','bank_country','bank_branch_name','bank_swift_code','business_nature',
//        'credit_days','credit_limit','preferred_product'
        ];
}
