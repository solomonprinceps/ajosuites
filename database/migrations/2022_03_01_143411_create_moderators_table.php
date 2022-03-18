<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModeratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moderators', function (Blueprint $table) {
            $table->id();
            $table->string("business_id")->nullable();
            $table->string("moderator_id")->nullable();
            $table->string("phone")->unique()->nullable();
            $table->string("name")->nullable();
            $table->string("logo")->nullable();
            $table->string('email')->unique()->nullable();
            $table->string("transaction_pin")->nullable();
            $table->string("password")->nullable();
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
        Schema::dropIfExists('moderators');
    }
}
