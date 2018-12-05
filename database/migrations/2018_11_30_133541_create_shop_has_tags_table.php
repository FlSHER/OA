<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopHasTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_has_tags', function (Blueprint $table) {
            $table->char('shop_sn', 10)->commit('店铺编号');
            $table->integer('tag_id')->unsigned()->comment('标签ID');
            
            $table->unique(['shop_sn', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_has_tags');
    }
}
