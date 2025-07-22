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
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');;
            $table->string('email');
            $table->foreignId('user_address_id')->constrained('user_addresses')->onDelete('cascade');
            $table->double('shipping_price')->nullable()->default(0.0);
            $table->double('tax')->nullable()->default(0.0);
            $table->double('grand_total')->default(0.0);
            $table->integer('qty')->default(1);
            $table->integer('shipping_method_id')->nullable()->default(0);
            $table->integer('payment_method_id')->nullable()->default(0);
            $table->string('order_status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->timestamps();
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
