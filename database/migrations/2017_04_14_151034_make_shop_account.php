<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeShopAccount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('shops', function (Blueprint $table) {
            $table->char('salt', 6)->default('000000')->after('name');
            $table->char('password', 64)->comment('店铺密码')->default('34007657c850a84fd4e2c1bb7b6b1433ca37af6c003d423d03a04ded7c13b13f')->after('name');
            $table->char('ip_addr', 15)->comment('店铺访问ip')->default('')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('ip_addr');
            $table->dropColumn('password');
            $table->dropColumn('salt');
        });
    }

}
