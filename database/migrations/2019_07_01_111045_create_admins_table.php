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
            $table->enum('image_hosting', ['enabled', 'disabled', 'admin_enabled'])->default('disabled')->comment('是否开启图床');
            $table->boolean('is_binded')->default(0)->comment('是否已绑定');
            $table->boolean('is_image_home')->default(0)->comment('是否将图床设为首页');
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
