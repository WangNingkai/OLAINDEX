<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('original_url', 255);
            $table->string('short_code', 16);
            $table->timestamp('created_at', 0)->nullable();
            $table->index('short_code', 'idx_code');
        });
        Schema::table('short_urls', function (Blueprint $table) {
            $table->index('short_code', 'idx_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('short_urls');
    }
}
