<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThemeToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'theme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('theme', 50)->default('default')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'theme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('theme');
            });
        }
    }
}
