<?php

namespace App\Actions\Category;

use App\DTOs\Category\CategoryData;
use App\Models\Category;

class UpdateCategoryAction
{
    /**
     * Update an existing category with the provided DTO.
     */
    public function handle(Category $category, CategoryData $data): Category
    {
        $category->update([
            'name' => $data->name,
            'code' => strtoupper($data->code),
            'description' => $data->description,
            'is_active' => $data->isActive,
        ]);

        return $category;
    }
}
