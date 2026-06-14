<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('invoice_no')->unique();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('do_id');
            $table->unsignedBigInteger('vendor_id');
            $table->date('invoice_date');
            $table->string('customer_no')->nullable();
            $table->decimal('line_total', 15, 2);
            $table->decimal('service_tax', 15, 2);
            $table->decimal('shipping', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('penalty', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('payments', 15, 2)->default(0);
            $table->decimal('credits', 15, 2)->default(0);
            $table->decimal('financial_charges', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2);
            $table->string('payment_terms')->default('30 DAYS');
            $table->date('due_date');
            $table->string('proof_of_delivery')->nullable();
            $table->enum('status', ['Submitted', 'Finance Review', 'Payment Processing', 'Paid'])->default('Submitted');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->foreign('do_id')->references('do_id')->on('delivery_orders')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}