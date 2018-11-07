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
            ['name' => '筹备', 'sort' => '1'],
            ['name' => '开店', 'sort' => '2'],
            ['name' => '闭店', 'sort' => '3'],
        ];
        DB::table('shop_status')->truncate();
        DB::table('shop_status')->insert($data);
    }

}
