<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProfileIdAddTwoColumnsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('profile_id');
            $table->unsignedInteger('active_profile_id');
            $table->unsignedInteger('passive_profile_id');
            $table->foreign('active_profile_id')->references('id')->on('profiles');
            $table->foreign('passive_profile_id')->references('id')->on('profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedInteger('profile_id');
            $table->dropColumn('active_profile_id');
            $table->dropColumn('passive_profile_id');
            $table->foreign('profile_id')->references('id')->on('profiles');
        });
    }
}
