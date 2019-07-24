<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->comment('任务状态');
            $table->string('gid')->nullable()->comment('gid');
            $table->string('path')->comment('第一个文件路径');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('failed_at')->nullable()->comment('失败时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
