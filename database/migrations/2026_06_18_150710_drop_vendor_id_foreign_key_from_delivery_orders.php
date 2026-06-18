<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['vendor_id']);
        });
    }

    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            // Re-add the foreign key constraint if needed
            $table->foreign('vendor_id')->references('supplierid')->on('vendors')->onDelete('cascade');
        });
    }
};