<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsAndDealItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('deals')) {
            Schema::create('deals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->default(0)->index();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->enum('discount_type', ['fixed', 'percent'])->default('percent');
                $table->decimal('discount_value', 10, 2)->default(0.00);
                $table->decimal('original_price', 12, 2)->default(0.00);
                $table->decimal('discounted_price', 12, 2)->default(0.00);
                $table->integer('duration_days')->default(1);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable()->index();
                $table->tinyInteger('status')->default(1)->index();
                $table->unsignedInteger('orders_count')->default(0)->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('deal_items')) {
            Schema::create('deal_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('deal_id')->index();
                $table->unsignedBigInteger('item_id')->index();
                $table->decimal('original_price', 12, 2)->default(0.00);
                $table->decimal('discounted_price', 12, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deal_items');
        Schema::dropIfExists('deals');
    }
}
