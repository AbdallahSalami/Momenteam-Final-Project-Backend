<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('userId'); // Add the userId column
            $table->foreign('memberId')->references('id')->on('memberDetails')
                ->onDelete('cascade')
                ->onUpdate('cascade'); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
     
    }
    
};
