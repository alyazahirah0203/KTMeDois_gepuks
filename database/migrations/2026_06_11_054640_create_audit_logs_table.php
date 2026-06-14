<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->timestamp('timestamp')->useCurrent();
            $table->string('username');
            $table->string('module');
            $table->string('action');
            $table->text('description');
            $table->enum('status', ['Success', 'Failed']);
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}