<?php

declare(strict_types=1);

namespace App\Service\Search\Dto;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Support\Arr;

/**
 * Class SuggestResultDto.
 */
class SuggestCompletionResultDto
{
    /**
     * @var SuggestCompletionResultItemDto[]
     */
    public array $items = [];

    /**
     * @return array
     */
    public function getSuggest(): array
    {
        return Arr::map($this->items, function (SuggestCompletionResultItemDto $item) {
            return [
                'description' => $item->description,
                'slug'        => match (get_class($item->model)) {
                    Product::class => route('product', ['alias' => $item->model->slug]),
                    Category::class => route('category', ['alias' => $item->model->slug]),
                    Brand::class => route('brand', ['alias' => $item->model->slug]),
                },
            ];
        });
    }
}
