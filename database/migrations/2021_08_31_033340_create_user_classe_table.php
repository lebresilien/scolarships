<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserClasseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_classe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->contrained();
            $table->foreignId('school_id')->contrained();
            $table->foreignId('classe_id')->nullable()->contrained();
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
        Schema::dropIfExists('user_classe');
    }
}
