<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavinglogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savinglogs', function (Blueprint $table) {
            $table->id();
            $table->string("savings_serial")->nullable();
            $table->string("savinglog_id")->nullable();
            $table->string("saver_id")->nullable();
            $table->string("moderator_id")->nullable();
            $table->string("business_id")->nullable();
            $table->string("savings_type")->nullable();
            $table->string("paid_date")->nullable();
            $table->string("expected_paid_date")->nullable();
            $table->string("saving_amount")->nullable();
            $table->boolean("status")->default(0)->nullable();
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
        Schema::dropIfExists('savinglogs');
    }
}
