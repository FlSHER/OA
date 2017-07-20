<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffRelativesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('staff_relatives', function (Blueprint $table) {
            $table->mediumInteger('staff_sn');
            $table->mediumInteger('relative_sn')->comment('关联员工编号');
            $table->char('relative_name', 10)->comment('关联员工姓名');
            $table->tinyInteger('relative_type')->comment('关系类型');
            $table->timestamps();
            $table->primary(['staff_sn', 'relative_sn']);
        });
        Schema::create('staff_relative_type', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 5);
            $table->tinyInteger('group_id')->unsigned();
            $table->tinyInteger('opposite_group_id')->unsigned();
            $table->tinyInteger('gender_id')->unsigned()->default(0);
            $table->tinyInteger('sort')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('staff_relatives');
        Schema::dropIfExists('staff_relative_type');
    }

}
