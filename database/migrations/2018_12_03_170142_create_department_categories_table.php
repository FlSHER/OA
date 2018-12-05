<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 10)->comment('分类名称');
            $table->char('fields')->nullable()->comment('私有字段');
            $table->tinyInteger('is_locked')->default(0)->comment('是否锁定');

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
        Schema::dropIfExists('department_categories');
    }
}
