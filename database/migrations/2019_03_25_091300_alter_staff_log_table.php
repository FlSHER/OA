<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStaffLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_log', function (Blueprint $table) {
            $table->tinyInteger('is_show')->default(0)->comment('是否显示在时间轴');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_log', function (Blueprint $table) {
            $table->dropColumn('is_show');
        });
    }
}
