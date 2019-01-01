<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequisitionActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('requisition_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_type_id');
            $table->integer('requisition_id');
            $table->enum('action',['APPROVED', 'HOLD','REJECT']);
            $table->integer('action_taken_by');
            $table->text('comments');
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
         Schema::dropIfExists('requisition_activity');
    }
}
