<?php

namespace App\Livewire\Admin\Category;

use App\Actions\Category\CreateCategoryAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Forms\Category\CategoryForm;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryFormModal extends Component
{
    public CategoryForm $form;

    public bool $showModal = false;

    public bool $isEditing = false;

    #[On('open-category-modal')]
    public function open(?int $id = null): void
    {
        $this->form->resetForm();

        if ($id) {
            $category = Category::findOrFail($id);
            $this->form->setCategory($category);
            $this->isEditing = true;
        } else {
            $this->isEditing = false;
        }

        $this->showModal = true;
    }

    public function create(): void
    {
        $this->open();
    }

    public function edit(int|Category $id): void
    {
        $categoryId = $id instanceof Category ? $id->id : $id;
        $this->open($categoryId);
    }

    public function save(CreateCategoryAction $createAction, UpdateCategoryAction $updateAction): void
    {
        $this->form->validate();

        $dto = $this->form->toDto();

        if ($this->isEditing && $this->form->category) {
            $updateAction->handle($this->form->category, $dto);
            $message = "Kategori '{$dto->name}' berhasil diperbarui.";
        } else {
            $createAction->handle($dto);
            $message = "Kategori '{$dto->name}' berhasil ditambahkan.";
        }

        $this->showModal = false;
        $this->form->resetForm();
        $this->dispatch('category-saved', message: $message);
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->form->resetForm();
    }

    public function render(): View
    {
        return view('livewire.admin.category.category-form-modal');
    }
}
