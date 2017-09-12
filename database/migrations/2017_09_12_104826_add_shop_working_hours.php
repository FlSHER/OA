<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShopWorkingHours extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('shops', function (Blueprint $table) {
            $table->time('clock_in')->default('9:00:00')->after('address')->comment('上班时间');
            $table->time('clock_out')->default('18:00:00')->after('address')->comment('下班时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('clock_in');
            $table->dropColumn('clock_out');
        });
    }

}
