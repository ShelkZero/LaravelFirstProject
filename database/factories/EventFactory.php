<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Intervention\Image\ImageManagerStatic as Image;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $directory = storage_path('app/public/events');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $imageName = $this->faker->word . '.jpg';
        $imagePath = $directory . '/' . $imageName;

        $img = Image::canvas(800, 800, '#ffffff'); 

        for ($i = 0; $i < 50; $i++) {
            $x = rand(1, 800);
            $y = rand(1, 800);
            $width = rand(50, 150);
            $height = rand(50, 150);
            $color = $this->faker->hexColor;

            $img->rectangle($x, $y, $x + $width, $y + $height, function ($draw) use ($color) {
                $draw->background($color);
            });
        }

        $img->save($imagePath);

        $relativeImagePath = 'events/' . $imageName;

        return [
            'name' => $this->faker->sentence,
            'poster' => $relativeImagePath,
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'venue_id' => Venue::inRandomOrder()->first()->id,
        ];
    }
}

