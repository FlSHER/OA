<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDepartmentV1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->tinyInteger('cate_id')->default(0)->comment('关联部门分类');
            $table->mediumInteger('province_id')->default(0)->comment('部门省份');
            $table->char('minister_sn', 6)->default('')->comment('部长员工编号');
            $table->char('minister_name', 10)->default('')->comment('部长姓名');
            $table->integer('source_id')->default(0)->comment('关联钉钉部门ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('cate_id');
            $table->dropColumn('province_id');
            $table->dropColumn('minister_sn');
            $table->dropColumn('minister_name');
            $table->dropColumn('source_id');
        });
    }
}
