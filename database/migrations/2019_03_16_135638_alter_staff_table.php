<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->char('last_position', 20)->default('')->comment('最后调动职位');
            $table->char('latest_position', 20)->default('')->comment('最新调动职位');
            $table->date('last_position_at')->nullable()->comment('最后调动时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('last_position');
            $table->dropColumn('latest_position');
            $table->dropColumn('last_position_at');
        });
    }
}
