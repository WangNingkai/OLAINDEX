<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('remark',16)->default('');
            $table->string('accountType',8)->default('');
            $table->string('clientId',128)->default('');
            $table->string('clientSecret',128)->default('');
            $table->string('redirectUri',128)->default('');
            $table->text('accessToken');
            $table->text('refreshToken');
            $table->integer('tokenExpires');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('accounts');
    }
}
