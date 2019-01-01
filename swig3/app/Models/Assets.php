<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model

{
    protected $table = 'ac_assets';
    
    protected $fillable = ['code','name','alias_name','company_id','ledger_group_id','barcode_id','supplier_id',
                            'purchased_emp_id','expiry_year_count','expiry_month_count','purchase_date','purchase_value','asset_value'
                          ];
}
