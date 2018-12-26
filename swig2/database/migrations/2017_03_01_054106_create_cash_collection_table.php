<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_collection', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pos_ids');
            $table->integer('employee_id');
            $table->integer('bank_id');
            $table->string('ref_no');
            $table->string('amount');
            $table->enum('submited_by',['TOP_CASHIER', 'SUPERVISOR']);
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
        Schema::dropIfExists('cash_collection');
    }
}
