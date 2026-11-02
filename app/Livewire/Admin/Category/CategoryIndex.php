<?php

namespace App\Livewire\Admin\Category;

use App\Actions\Category\DeleteCategoryAction;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manajemen Kategori - Jangkau Samawa')]
class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public ?int $deletingCategoryId = null;

    public ?string $deletingCategoryName = null;

    public bool $showDeleteModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('open-category-modal');
    }

    public function openEditModal(int|Category $id): void
    {
        $categoryId = $id instanceof Category ? $id->id : $id;
        $this->dispatch('open-category-modal', id: $categoryId);
    }

    public function confirmDelete(int|Category $id): void
    {
        $categoryId = $id instanceof Category ? $id->id : $id;
        $category = Category::findOrFail($categoryId);

        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingCategoryId = null;
        $this->deletingCategoryName = null;
    }

    public function deleteCategory(DeleteCategoryAction $action): void
    {
        if (! $this->deletingCategoryId) {
            return;
        }

        $category = Category::findOrFail($this->deletingCategoryId);

        try {
            $action->handle($category);
            session()->flash('success', "Kategori '{$category->name}' berhasil dihapus.");
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->first('category') ?: $e->getMessage();
            session()->flash('error', $message);
        } catch (\Throwable $e) {
            session()->flash('error', 'Terjadi kendala saat menghapus kategori.');
        } finally {
            $this->cancelDelete();
        }
    }

    public function delete(int|Category $category, DeleteCategoryAction $action): void
    {
        $categoryId = $category instanceof Category ? $category->id : $category;
        $this->confirmDelete($categoryId);
        $this->deleteCategory($action);
    }

    #[On('category-saved')]
    public function handleCategorySaved(string $message): void
    {
        session()->flash('success', $message);
    }

    public function render(): View
    {
        $query = Category::query();

        if ($this->search !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('code', 'like', $searchTerm);
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query->latest('id')->paginate(10);

        return view('livewire.admin.category.category-index', [
            'categories' => $categories,
        ]);
    }
}
