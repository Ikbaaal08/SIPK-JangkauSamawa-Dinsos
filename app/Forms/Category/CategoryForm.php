<?php

namespace App\Forms\Category;

use App\DTOs\Category\CategoryData;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?Category $category = null;

    public string $name = '';

    public string $code = '';

    public string $description = '';

    public bool $is_active = true;

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->code = $category->code;
        $this->description = $category->description ?? '';
        $this->is_active = (bool) $category->is_active;
    }

    public function resetForm(): void
    {
        $this->reset(['category', 'name', 'code', 'description']);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function validate($rules = null, $messages = [], $attributes = []): array
    {
        $this->code = strtoupper(trim($this->code));

        return parent::validate($rules, $messages, $attributes);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('categories', 'code')->ignore($this->category?->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'code.required' => 'Kode kategori wajib diisi.',
            'code.string' => 'Kode kategori harus berupa teks.',
            'code.max' => 'Kode kategori maksimal 20 karakter.',
            'code.unique' => 'Kode kategori sudah digunakan. Gunakan kode lain.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'is_active.boolean' => 'Status aktif harus bernilai benar atau salah.',
        ];
    }

    public function toDto(): CategoryData
    {
        return new CategoryData(
            name: trim($this->name),
            code: strtoupper(trim($this->code)),
            description: trim($this->description) !== '' ? trim($this->description) : null,
            isActive: (bool) $this->is_active,
        );
    }
}
