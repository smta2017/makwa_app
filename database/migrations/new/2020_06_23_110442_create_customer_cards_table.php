<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->string('obj_id')->nullable();
            $table->string('token')->nullable();
            $table->string('masked_pan')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('card_subtype')->nullable();
            $table->string('email');
            $table->string('order_id');
            $table->string('user_added')->nullable();
            $table->string('is_default')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_cards');
    }
}
