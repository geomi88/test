<?php

use Illuminate\Database\Seeder;

class luxEstateDataSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //
        for ($i = 1; $i <= 20; $i++) {
            DB::table('municipalities')->insert([
                'name' => 'municipality no- ' . str_random(3),
                'status' => 1,
                'id' => $i
            ]);
        }
        for ($i = 0; $i < 20; $i++) {
            DB::table('agents')->insert([
                'name' => 'agent-' . str_random(3) . str_random(7),
                'municipality_id' => rand(1, 20),
                'status' => 1,
                'member_since' => '2017-11-15',
                'description' => str_random(255),
                'address' => str_random(10). ',' . str_random(10) . ' , ' . str_random(5),
                'email' => str_random(6) . str_random(3) . '@mailinator.com',
                'mobile_number' => str_random(10),
                'office_phone' => str_random(3) . '-' . str_random(6),
            ]);
        }
        for ($i = 0; $i < 100; $i++) {
            DB::table('neighbourhoods')->insert([
                'name' => 'neibrhd -' . str_random(3) . str_random(7),
                'municipality_id' => rand(1, 20),
                'status' => 1,
                'description' => str_random(255),
                'address' => str_random(10). ',' . str_random(10) . ',' . str_random(5),
            ]);
        }
    }

}
