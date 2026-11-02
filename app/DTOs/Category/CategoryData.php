<?php

namespace App\DTOs\Category;

readonly class CategoryData
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $description = null,
        public bool $isActive = true,
    ) {}
}
