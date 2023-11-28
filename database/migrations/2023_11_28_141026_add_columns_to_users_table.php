<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->text('thumb')->nullable();
            $table->text('thumb_file')->nullable();
            $table->text('notes')->nullable();
            $table->text('status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('thumb');
            $table->dropColumn('thumb_file');
            $table->dropColumn('notes');
            $table->dropColumn('status');
        });
    }
}
