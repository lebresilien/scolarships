<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('classrooms', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('academies', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('extensions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('sequences', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('notes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('sequences', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('extensions', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('academies', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
