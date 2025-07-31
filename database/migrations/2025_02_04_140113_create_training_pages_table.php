<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTrainingPagesTable extends Migration
{
    public function up()
    {
        // Create table
        DB::statement("
            CREATE TABLE training_pages (
                id SERIAL PRIMARY KEY,
                page_number INTEGER NOT NULL UNIQUE,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                video_id VARCHAR(11) NULL,
                created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Add index separately
        DB::statement("
            CREATE UNIQUE INDEX training_pages_page_number_unique 
            ON training_pages (page_number)
        ");
    }

    public function down()
    {
        DB::statement("DROP TABLE IF EXISTS training_pages");
    }
}