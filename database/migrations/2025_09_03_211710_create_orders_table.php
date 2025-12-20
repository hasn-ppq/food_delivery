<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('restaurant_id')->index();
            $table->unsignedBigInteger('delivery_id')->nullable()->index();
            $table->decimal('total_price', 10, 2);
            $table->decimal('delivery_price', 8, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('status', ['pending', 'accepted', 'cooking','ready_to_receive' ,'on_the_way', 'delivered', 'canceled'])->default('pending');
            $table->string('customer_address')->nullable();
            $table->decimal('customer_lat', 10, 7)->nullable();
            $table->decimal('customer_lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('delivery_assigned_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('canceled_reason')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('delivery_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
