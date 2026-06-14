<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id('do_id');
            $table->string('do_number')->unique();
            $table->string('po_number');
            $table->unsignedBigInteger('vendor_id');
            $table->date('order_date');
            $table->text('shipping_address');
            $table->text('invoice_address');
            $table->date('delivery_date');
            $table->time('delivery_time');
            $table->text('remarks')->nullable();
            $table->string('receiver_signature')->nullable();
            $table->enum('status', ['Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected'])->default('Draft');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_orders');
    }
}