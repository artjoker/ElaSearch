<?php

declare(strict_types=1);

namespace App\Service\Admin\Search\Dto;

use Illuminate\Support\Arr;

/**
 * Class SearchResultDto.
 */
class SearchResultDto
{
    /**
     * @var SearchResultItemDto[]
     */
    public array $items = [];

    /**
     * @var int
     */
    public int $total = 0;

    /**
     * @return array
     */
    public function getModels(): array
    {
        return Arr::map($this->items, function (SearchResultItemDto $item) {
            return $item->model;
        });
    }
}
