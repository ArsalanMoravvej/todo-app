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
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['todo', 'in-progress', 'done'])
                ->nullable()
                ->default('todo');
            $table->tinyInteger('priority')
                ->unsigned()
                ->nullable()
                ->default(3)
                ->comment('1=High, 2=Medium, 3=Low');
            $table->softDeletes();
            $table->timestamps();
        });

        //Check for the priority column being in the range.
        DB::statement(
            /** @lang text */
            'ALTER TABLE todos
            ADD CONSTRAINT check_priority
            CHECK (priority >= 1 AND priority <= 3)'
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
