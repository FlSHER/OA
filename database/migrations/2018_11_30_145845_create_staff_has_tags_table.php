<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffHasTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_has_tags', function (Blueprint $table) {
            $table->char('staff_sn', 10)->commit('店铺编号');
            $table->integer('tag_id')->unsigned()->comment('标签ID');
            
            $table->unique(['staff_sn', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_has_tags');
    }
}
