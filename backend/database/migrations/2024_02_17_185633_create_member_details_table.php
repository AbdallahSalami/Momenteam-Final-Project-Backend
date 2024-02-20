
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
        Schema::create('memberDetails', function (Blueprint $table) {
            $table->id();   
            $table->unsignedBigInteger('userId')->useCurrent()->unique();
            $table->string('statusDegree');
            $table->string('majorDegree');
            $table->string('year');
            $table->timestamp('dateOfJoining');
            // $table->unsignedBigInteger('financialStateId')->useCurrent();
            $table->string('location');
            $table->timestamp('dateOfCreation')->useCurrent();
            $table->enum('status', ['active', 'inactive', 'finshed','pending']);
            $table->timestamps();


            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');


            // $table->foreign('financialStateId')->references('id')->on('financialState')
            // ->onDelete('cascade')
            // ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberDetails');
    }
};
