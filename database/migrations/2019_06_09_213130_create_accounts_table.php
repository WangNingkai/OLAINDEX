<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('account_type', 10)->default('com')->comment('账户类型');
            $table->string('slug')->default('')->comment('标识');
            $table->string('account_email')->default('')->comment('账户邮箱');
            $table->text('access_token')->nullable(false)->comment('access_token');
            $table->text('refresh_token')->nullable(false)->comment('refresh_token');
            $table->timestamp('access_token_expires')->nullable()->comment('超时时间');
            $table->boolean('status')->default(1)->comment('状态');
            $table->mediumText('extend')->nullable()->comment('其他');
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
