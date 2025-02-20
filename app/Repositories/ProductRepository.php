<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function updateOrCreate(array $attributes)
    {
        return Product::updateOrCreate(
            ['title' => $attributes['title']],
            [
                'price' => $attributes['price'],
                'link' => $attributes['link'],
                'category_id' => $attributes['category_id'],
                'image' => $attributes['image'],
                'description' => $attributes['description']
            ]
        );
    }
}