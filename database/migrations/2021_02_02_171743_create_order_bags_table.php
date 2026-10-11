<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id');

            $table->unsignedInteger('created_by')->default(0);
            $table->timestamp('delivery_sorted')->nullable();
            $table->timestamp('pickup_sorted')->nullable();

            $table->integer('bag_id');
            $table->timestamp('from_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * 
     */
    public function down()
    {
        Schema::dropIfExists('order_bags');
    }
}
