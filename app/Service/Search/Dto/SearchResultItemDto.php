<?php

declare(strict_types=1);

namespace App\Service\Search\Dto;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Product;

/**
 * Class SearchResultItemDto.
 */
class SearchResultItemDto
{
    /**
     * @var float
     */
    public float $score;

    /**
     * @var Category|Product|Brand
     */
    public Category|Product|Brand $model;
}
