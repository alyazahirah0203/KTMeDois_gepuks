<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('supplierid', 25)->unique();
            $table->string('supplier_comp_reg_no', 200)->nullable();
            $table->string('supplier_comp_name', 200)->nullable();
            $table->string('supplier_ctc_no', 200)->nullable();
            $table->string('supplier_ctc_person', 100)->nullable();
            $table->string('supplier_email_add', 200)->nullable();
            $table->date('supplier_expired_date')->nullable();
            $table->enum('supplier_ctc_status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}