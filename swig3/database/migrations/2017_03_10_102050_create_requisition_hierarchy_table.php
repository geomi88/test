<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequisitionHierarchyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('requisition_hierarchy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_type_id');
            $table->enum('approver_type',['TOP_MANAGER', 'CEO']);            
            $table->integer('approver_type_value');
            $table->integer('level');
             $table->integer('status');
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
        Schema::dropIfExists('requisition_hierarchy');
    }
}
