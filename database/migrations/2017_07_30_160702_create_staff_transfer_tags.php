<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTransferTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_transfer_tags', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('name',10)->comment('标签名称');
            $table->tinyInteger('sort')->unsigned()->comment('排序');
        });
        
        Schema::create('staff_transfer_has_tags', function (Blueprint $table) {
            $table->integer('transfer_id');
            $table->smallInteger('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_transfer_tags');
        Schema::dropIfExists('staff_transfer_has_tags');
    }
}
