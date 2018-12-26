<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequesitionTabless extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
      Schema::create('requisition', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_type_id');
            $table->integer('parent_id');
            $table->integer('created_by');
            $table->string('title');
            $table->text('description');
            $table->integer('amount');
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
         Schema::dropIfExists('requisition');
    }
}
