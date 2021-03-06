<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->call(StaffRelativeTypeSeeder::class);
        // $this->call(StaffTransferTagSeeder::class);
        $this->call(ShopStatusSeeder::class);
    }

}
