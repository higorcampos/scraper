<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function updateOrCreate(array $attributes)
    {
        return Category::updateOrCreate(
            ['name' => $attributes['name']],
            ['link' => $attributes['link']]
        );
    }
}