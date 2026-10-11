<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingsFieldsOnReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('delivery_time_rating')->nullable();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('order_packaging_rating')->nullable();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('value_money_rating')->nullable();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('quality_cleaning_rating')->nullable();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('quality_ironning_rating')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('delivery_time_rating');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('order_packaging_rating');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('value_money_rating');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('quality_cleaning_rating');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('quality_ironning_rating');
        });
    }
}
