<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('firstName');
            $table->string('secondName');
            $table->string('lastName');
            $table->string('highestDegree');
            $table->string('major');
            $table->string('educationalInstitution');
            $table->integer('phoneNumber')->unique();
            $table->string('emailVerification'); // must take 
            $table->unsignedBigInteger('roleId')->nullable();
            $table->timestamp('dateOfCreation')->useCurrent();
            $table->enum('status', ['active', 'inactive', 'pending']); // we must add verfied
            $table->timestamps();

            $table->foreign('roleId')->references('id')->on('roles')
                ->onDelete('set null') // Set roleId to null if the associated role is deleted
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }   
};
