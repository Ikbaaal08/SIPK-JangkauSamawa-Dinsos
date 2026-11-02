<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

class DeleteCategoryAction
{
    /**
     * Delete the specified category if it has no associated complaints.
     *
     * @throws ValidationException
     */
    public function handle(Category $category): bool
    {
        if ($category->complaints()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori tidak dapat dihapus karena masih memiliki aduan terkait.',
            ]);
        }

        return (bool) $category->delete();
    }
}
