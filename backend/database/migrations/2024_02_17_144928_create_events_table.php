<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
           $table->string('title');
            $table->string('description');
            $table->timestamp('date');
            $table->timestamp('dateOfCreation')->useCurrent();
            $table->enum('status', ['active', 'inactive', 'finshed','pending']);
            $table->timestamps();
            $table->unsignedBigInteger('userId'); // Add the userId column
            $table->foreign('memberId')->references('id')->on('memberDetails')
                ->onDelete('cascade')
                ->onUpdate('cascade'); 
            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
