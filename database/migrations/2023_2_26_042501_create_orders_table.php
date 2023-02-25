<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->foreignId('book_appoinment_id')->nullable()->constrained('book_appoinments');
            $table->string('type');
            $table->string('transaction_id')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('gross_amount');
            $table->string('midtrans_token')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
