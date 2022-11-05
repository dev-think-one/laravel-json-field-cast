<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestbenchUpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('json_meta')->nullable();
            $table->text('text_meta')->nullable();

            $table->json('json_file')->nullable();
            $table->text('text_file')->nullable();

            $table->json('array_json_meta')->nullable();
            $table->text('array_text_meta')->nullable();

            $table->string('content_type')->nullable();
            $table->json('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('json_meta');
            $table->dropColumn('text_meta');

            $table->dropColumn('content_type');
            $table->dropColumn('content');
        });
    }
}
