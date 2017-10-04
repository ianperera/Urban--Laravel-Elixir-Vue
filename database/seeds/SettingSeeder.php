<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leadTime = factory(Setting::class)->create([
            'id' => 'lead_time',
            'title' => 'Lead Time',
            'description' => 'Lead Time',
            'value' => 7
            ]);
    }
}
