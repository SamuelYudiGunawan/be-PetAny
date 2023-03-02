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
            $table->integer('quantity')->nullable();
            $table->string('product_status')->nullable();
            $table->unsignedBigInteger('gross_amount');
            $table->string('payment_type')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('midtrans_token')->nullable();
            $table->string('status_code')->nullable();
            $table->json('json_data')->nullable();
            $table->string('signature_key')->nullable();
            $table->string('payment_url')->nullable();
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
