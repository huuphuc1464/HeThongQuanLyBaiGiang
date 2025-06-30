<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bai', function (Blueprint $table) {
            $table->longText('NoiDung')->change();
        });
    }

    public function down()
    {
        Schema::table('bai', function (Blueprint $table) {
            $table->text('NoiDung')->change();
        });
    }
};