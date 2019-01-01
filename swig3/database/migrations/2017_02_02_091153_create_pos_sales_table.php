<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('branch_id');
            $table->integer('job_shift_id');
            $table->string('total_sale');
            $table->string('cash_collection');
            $table->integer('reason_id');
            $table->string('reason_details');
            $table->integer('employee_id');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_sales');
    }
}
