<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Intervention\Image\Facades\Image;

class EventSeeder extends Seeder
{
    public function run()
    {
        Event::factory()->count(50)->create();
    }
}
