<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->string("savings_serial")->nullable();
            $table->string("status")->default('0')->nullable();
            $table->string("saver_id")->nullable();
            $table->string("moderator_id")->nullable();
            $table->string("business_id")->nullable();
            $table->string("savings_type")->nullable();
            $table->string("saving_amount")->nullable();
            $table->string("saving_total_amount")->nullable();
            $table->string("saving_interval")->nullable();
            $table->string("paid_interval")->default('0')->nullable();
            $table->string("start_date")->nullable();
            $table->string("end_date")->nullable();
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
        Schema::dropIfExists('savings');
    }
}
