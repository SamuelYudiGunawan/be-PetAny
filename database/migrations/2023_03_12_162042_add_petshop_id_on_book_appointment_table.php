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
        Schema::table('book_appoinments', function (Blueprint $table) {
            $table->foreignId('petshop_id')->constrained('petshops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_appoinments', function (Blueprint $table) {
            $table->dropForeign(['petshop_id']);
        });
    }
};
