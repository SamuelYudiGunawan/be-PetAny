<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('status', 'product_status');
            $table->string('status_code')->nullable();
            $table->json('json_data')->nullable();
            $table->string('signature_key')->nullable();
            $table->string('payment_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('product_status', 'status');
            $table->dropColumn(['status_code', 'json_data', 'signature_key', 'payment_url']);
        });
    }
};
