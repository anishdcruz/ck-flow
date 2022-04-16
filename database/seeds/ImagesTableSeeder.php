<?php

use Illuminate\Database\Seeder;
use App\Image;
use Faker\Factory;
class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Image::truncate();
        Storage::deleteDirectory('images');
        Storage::makeDirectory('images');
        foreach(range(1, 14) as $i) {
        	$image = $faker->image(storage_path('app/images'), mt_rand(280, 300), mt_rand(280, 300), 'cats', false);
        	Image::create([
        		'title' => 'Cat Pic '.$i,
        		'filename' => 'images/'.$image,
        		'extension' => 'jpg',
        		'size' => '7802',
        		'dimension' => '200x200'
        	]);
        }
    }
}
