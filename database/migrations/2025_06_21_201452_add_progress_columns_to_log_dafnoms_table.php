<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('log_dafnoms', function (Blueprint $table) {
            $table->unsignedInteger('progress_current')->default(0)->after('message');
            $table->unsignedInteger('progress_total')->default(0)->after('progress_current');
        });
    }

    public function down()
    {
        Schema::table('log_dafnoms', function (Blueprint $table) {
            $table->dropColumn(['progress_current', 'progress_total']);
        });
    }
};