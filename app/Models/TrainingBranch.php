<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingBranch extends Model
{
    protected $fillable = ['name', 'order'];

    public function pages(): HasMany
    {
        return $this->hasMany(TrainingPage::class, 'branch_id')->orderBy('order');
    }
}