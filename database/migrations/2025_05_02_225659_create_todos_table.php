<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['todo', 'in-progress', 'done'])
                ->default('todo');
            $table->tinyInteger('priority')
                ->unsigned()
                ->nullable()
                ->comment('1=High, 2=Medium, 3=Low');
            $table->softDeletes();
            $table->timestamps();
        });
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
            /** @lang text */
                'ALTER TABLE todos
            ADD CONSTRAINT check_priority
            CHECK (priority >= 1 AND priority <= 3)'
            );
        }
        //Check for the priority column being in the range.


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
