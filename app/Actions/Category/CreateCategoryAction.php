<?php

namespace App\Actions\Category;

use App\DTOs\Category\CategoryData;
use App\Models\Category;

class CreateCategoryAction
{
    /**
     * Create a new category from the provided DTO.
     */
    public function handle(CategoryData $data): Category
    {
        return Category::create([
            'name' => $data->name,
            'code' => strtoupper($data->code),
            'description' => $data->description,
            'is_active' => $data->isActive,
        ]);
    }
}
