<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function getCategories(): LengthAwarePaginator
    {
        return Category::latest()->get();
    }

    public function storeCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return $category->fresh();
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }
}