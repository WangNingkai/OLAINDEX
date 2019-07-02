<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOneDrivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_drives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->unsigned()->index()->comment('管理员ID');
            $table->boolean('is_default')->default(0)->comment('是否默认');
            $table->string('app_version')->comment('app版本');
            $table->string('root')->default('/')->comment('OneDrive根目录');
            $table->text('access_token')->nullable()->comment('访问token');
            $table->text('refresh_token')->nullable()->comment('刷新token');
            $table->integer('access_token_expires')->nullable()->comment('过期时间');
            $table->string('client_id')->comment('microsoftgraph连接id');
            $table->string('client_secret')->comment('microsoftgraph连接秘钥');
            $table->string('redirect_uri')->comment('重定向地址');
            $table->string('account_type')->nullable()->comment('onedrive账号类型');
            $table->text('settings')->nullable()->comment('设置项');
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
        Schema::dropIfExists('one_drives');
    }
}
