<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('matricule');
            $table->string('fname');
            $table->string('slug')->unique();
            $table->string('lname')->nullable();
            $table->string('sexe');

            $table->string('fathername');
            $table->string('mothername');
            $table->string('fphone');
            $table->string('mphone');
            $table->date('born_at');
            $table->text('allergie');
            $table->string('logo')->default('anonymous.png');
            $table->text('description');
            $table->string('quarter')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('students');
    }
}
