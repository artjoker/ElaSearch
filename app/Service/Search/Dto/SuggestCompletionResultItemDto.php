<?php

declare(strict_types=1);

namespace App\Service\Search\Dto;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\User;

/**
 * Class SuggestResultItemDto.
 */
class SuggestCompletionResultItemDto
{
    /**
     * @var string
     */
    public string $text;

    /**
     * @var Category|Product|Brand
     */
    public Category|Product|Brand $model;
}
