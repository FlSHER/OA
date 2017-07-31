<?php

use Illuminate\Database\Seeder;

class StaffTransferTagSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            ['name' => '休假结束', 'sort' => '1'],
            ['name' => '新人入职', 'sort' => '2'],
            ['name' => '新店开业', 'sort' => '3'],
            ['name' => '成都出发', 'sort' => '4'],
            ['name' => '濮院出发', 'sort' => '5'],
        ];
        DB::table('staff_transfer_tags')->truncate();
        DB::table('staff_transfer_tags')->insert($data);
    }

}
