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
            $table->integer('cover_id')->unsigned()->index()->comment('封面ID');
            $table->string('name')->comment('名称');
            $table->boolean('is_default')->default(0)->comment('是否默认');
            $table->boolean('is_configuraed')->default(0)->comment('是否配置client信息');
            $table->boolean('is_binded')->default(0)->comment('是否绑定');
            $table->string('app_version')->default('v3.2.1')->comment('app版本');
            $table->string('root')->default('/')->comment('OneDrive根目录');
            $table->text('access_token')->nullable()->comment('访问token');
            $table->text('refresh_token')->nullable()->comment('刷新token');
            $table->integer('access_token_expires')->nullable()->comment('过期时间');
            $table->integer('expires')->default(900)->comment('缓存时间');
            $table->string('client_id')->nullable()->comment('microsoftgraph连接id');
            $table->string('client_secret')->nullable()->comment('microsoftgraph连接秘钥');
            $table->string('redirect_uri')->nullable()->comment('重定向地址');
            $table->string('account_type')->nullable()->comment('onedrive账号类型');
            $table->text('settings')->nullable()->comment('设置项');
            $table->timestamps();
            $table->softDeletes();
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
