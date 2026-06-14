<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoItemsTable extends Migration
{
    public function up()
    {
        Schema::create('do_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('do_id');
            $table->string('item_no');
            $table->string('description');
            $table->integer('quantity');
            $table->timestamps();
            
            $table->foreign('do_id')->references('do_id')->on('delivery_orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('do_items');
    }
}