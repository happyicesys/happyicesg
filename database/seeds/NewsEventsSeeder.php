<?php

use Illuminate\Database\Seeder;
use App\NewsEvents;

class NewsEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NewsEvents::create([
            'src'=>'newsevents/news1.jpg',
            'alt'=>'newsevents/news1.jpg',
        ]);

        NewsEvents::create([
            'src'=>'newsevents/news2.jpg',
            'alt'=>'newsevents/news2.jpg',
        ]); 

        NewsEvents::create([
            'src'=>'newsevents/news3.jpg',
            'alt'=>'newsevents/news3.jpg',
        ]);                
    }
}
