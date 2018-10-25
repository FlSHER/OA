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
            ['id' => 1, 'name' => '开店', 'sort' => '1'],
            ['id' => 0, 'name' => '闭店', 'sort' => '2'],
        ];
        DB::table('shop_status')->truncate();
        DB::table('shop_status')->insert($data);
    }

}
