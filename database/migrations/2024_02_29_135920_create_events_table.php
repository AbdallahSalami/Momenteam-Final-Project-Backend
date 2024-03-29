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
                $table->unsignedBigInteger('memberId');
                $table->string('title');
                $table->string('description');
                $table->dateTime('date');
                $table->string('location');
                $table->timestamp('dateOfCreation')->useCurrent();
                $table->enum('status', ['active', 'inactive', 'finshed','pending']);
                $table->timestamps();

                $table->foreign('memberId')->references('id')->on('memberDetails')
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
