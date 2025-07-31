<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('training_branches', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Branch name (e.g., "Newbie", "Advanced")
        $table->integer('order')->default(0); // For sorting branches
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('training_branches');
}  
};


