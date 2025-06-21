<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // PENTING: Kolom username
            $table->string('password');
            $table->string('name')->nullable();
            $table->string('nip')->nullable();
            $table->timestamp('tgl_dibuat')->nullable();
            $table->date('tgl_berlaku')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};