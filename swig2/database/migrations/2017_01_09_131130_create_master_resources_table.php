<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('resource_type',['BRANCH', 'REGION']);
            $table->string('name');
            $table->string('alias_name');
            $table->integer('company_id');
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
        Schema::dropIfExists('master_resources');
    }
}
