<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('training_pages')->insert([
        [
            'page_number' => 1,
            'title' => 'Introduction to Missionary Work',
            'content' => '<p>Welcome to your first training session...</p>',
            'video_id' => 'dQw4w9WgXcQ' // Example YouTube ID
        ],
        [
            'page_number' => 2,
            'title' => 'Cultural Sensitivity',
            'content' => '<p>Understanding local customs...</p>',
            'video_id' => 'abcdefghijk'
        ],
        // Add entries for all 10 pages
    ]);
}
}
