<?php

declare(strict_types=1);

namespace App\Service\Admin\Search\Dto;

use App\Enums\Post\PostType;
use App\Models\Card\Card;
use App\Models\Post\Post;
use App\Models\User;
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
                'text' => $item->text,
                'link' => match (get_class($item->model)) {
                    Card::class => route('getCard', ['alias' => $item->model->link]),
                    Post::class => route($item->model->type_id == PostType::Article->value ? 'frontend.articles.post' : 'frontend.blog.post', ['alias' => $item->model->link]),
                    User::class => route('frontend.author.index', ['alias' => $item->model->link]),
                },
            ];
        });
    }
}
