<?php

use Illuminate\Database\Seeder;

class ShopStatusSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            ['name' => '未营业', 'sort' => 1],
            ['name' => '营业中', 'sort' => 2],
            ['name' => '闭店', 'sort' => 3],
            ['name' => '取消', 'sort' => 4],
        ];
        DB::table('shop_status')->truncate();
        DB::table('shop_status')->insert($data);
    }
 
}
