<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('officers', function (Blueprint $table) {
            $table->id('staff_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('staff_name');
            $table->string('department');
            $table->string('position');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('officers');
    }
};