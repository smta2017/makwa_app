<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('type', ['fixed', 'percentage'])->nullable();
            $table->float('discount_figure')->nullable();
            $table->float('cap')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->float('min_order')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('is_archive')->nullable();
            $table->string('title')->nullable();
            $table->string('message')->nullable();
            $table->tinyInteger('show_in_app')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->integer('applay_on_delivery')->nullable();
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
        Schema::dropIfExists('vendor_promotions');
    }
}
