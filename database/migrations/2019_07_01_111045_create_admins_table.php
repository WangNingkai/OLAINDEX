<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('site_name')->default('OLAINDEX')->comment('站点名称');
            $table->string('theme')->default('cosmo')->comment('站点主题');
            $table->string('hotlink_protection')->nullable()->comment('防盗链');
            $table->string('copyright')->nullable()->comment('自定义版权显示');
            $table->string('statistics')->nullable()->comment('统计代码');
            $table->string('tfa_secret', 16)->nullable()->comment('google2fa二步验证秘钥');
            $table->boolean('is_binded')->default(0)->comment('是否已绑定onedrive');
            $table->boolean('is_tfa')->default(0)->comment('是否已绑定google2fa');
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
        Schema::dropIfExists('admins');
    }
}
