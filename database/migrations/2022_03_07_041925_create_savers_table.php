<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string("saver_id")->nullable();
            $table->string("moderator_id")->nullable();
            $table->string("business_id")->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('otp')->unique()->nullable();
            $table->string('total_amount')->default("0")->nullable();
            $table->string("total_savings")->default("0")->nullable();
            $table->string('logo')->nullable();
            $table->string("status")->default("0")->nullable();
            $table->text('email_recievers')->nullable();
            $table->string('password')->nullable();
            $table->string('password_string')->nullable();
            $table->text("address")->default("")->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('savers');
    }
}
