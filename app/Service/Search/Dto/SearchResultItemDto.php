<?php

declare(strict_types=1);

namespace App\Service\Admin\Search\Dto;

use App\Models\Card\Card;
use App\Models\Post\Post;
use App\Models\User;

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
     * @var Card|Post|User
     */
    public Card|Post|User $model;
}
