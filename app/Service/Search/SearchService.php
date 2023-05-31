<?php

declare(strict_types=1);

namespace App\Service\Search;

use App\Models\Card\Card;
use App\Models\Post\Post;
use App\Models\User;
use App\Repositories\Search\SearchRepository;
use App\Service\Search\Dto\SearchResultDto;
use App\Service\Search\Dto\SearchResultItemDto;
use App\Service\Search\Dto\SuggestCompletionResultDto;
use App\Service\Search\Dto\SuggestCompletionResultItemDto;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class SearchService.
 */
class SearchService
{
    /**
     * @var array|string[]
     */
    private array $indexMap = [
        Post::class => 'posts',
        Card::class => 'cards',
        User::class => 'users',
    ];

    /**
     * @param SearchRepository $searchRepository
     */
    public function __construct(
        private readonly SearchRepository $searchRepository,
    ) {
    }

    /**
     * @param string $index
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return bool
     */
    public function createIndex(string $index): bool
    {
        if (class_exists($index)) {
            $index = (new $index())->getTable();
        }

        $settings = [
            'analysis' => [
                'analyzer' => [
                    'my_analyzer' => [
                        'type'      => 'custom',
                        'tokenizer' => 'standard',
                        'filter'    => [
                            'lowercase',
                        ],
                        'char_filter' => [
                            'html_strip',
                        ],
                    ],
                    'my_stop_analyzer' => [
                        'type'      => 'custom',
                        'tokenizer' => 'standard',
                        'filter'    => [
                            'lowercase',
                            'english_stop',
                        ],
                        'char_filter' => [
                            'html_strip',
                        ],
                    ],
                    'trigram' => [
                        'type'      => 'custom',
                        'tokenizer' => 'standard',
                        'filter'    => [
                            'lowercase',
                            'shingle',
                        ],
                    ],
                    'reverse' => [
                        'type'      => 'custom',
                        'tokenizer' => 'standard',
                        'filter'    => [
                            'lowercase',
                            'reverse',
                        ],
                    ],
                ],
                'filter' => [
                    'english_stop' => [
                        'type'      => 'stop',
                        'stopwords' => '_english_',
                    ],
                    'shingle' => [
                        'type'             => 'shingle',
                        'min_shingle_size' => 2,
                        'max_shingle_size' => 3,
                    ],
                ],
            ],
        ];

        $mapping = [
            'properties' => [
                'id' => [
                    'type' => 'long',
                ],
                'published' => [
                    'type' => 'keyword',
                ],
                'title' => [
                    'type'   => 'text',
                    'fields' => [
                        'trigram' => [
                            'type'     => 'text',
                            'analyzer' => 'trigram',
                        ],
                        'reverse' => [
                            'type'     => 'text',
                            'analyzer' => 'reverse',
                        ],
                        'completion' => [
                            'type'     => 'completion',
                            'contexts' => [
                                [
                                    'name' => 'published',
                                    'type' => 'category',
                                    'path' => 'published',
                                ],
                            ],
                        ],
                    ],
                ],
                'content' => [
                    'type'                  => 'text',
                    'analyzer'              => 'my_analyzer',
                    'search_analyzer'       => 'my_stop_analyzer',
                    'search_quote_analyzer' => 'my_analyzer',
                    'fields'                => [
                        'trigram' => [
                            'type'     => 'text',
                            'analyzer' => 'trigram',
                        ],
                        'reverse' => [
                            'type'     => 'text',
                            'analyzer' => 'reverse',
                        ],
                    ],
                ],
            ],
        ];

        return $this->searchRepository->createIndex($index, $settings, $mapping);
    }

    /**
     * @param string $index
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return bool
     */
    public function deleteIndex(string $index): bool
    {
        if (class_exists($index)) {
            $index = (new $index())->getTable();
        }

        return $this->searchRepository->deleteIndex($index);
    }

    /**
     * @param string $index
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return bool
     */
    public function flush(string $index): bool
    {
        if (class_exists($index)) {
            $index = (new $index())->getTable();
        }

        $this->searchRepository->deleteIndex($index);

        return $this->createIndex($index);
    }

    /**
     * @param Card|Post|User $model
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return bool
     */
    public function index(Card|Post|User $model): bool
    {
        $result = $this->searchRepository->index(
            $model->getTable(),
            $model->getKey(),
            $model->toSearchableArray(),
        );

        return key_exists('_id', $result);
    }

    /**
     * @param string     $index
     * @param int|string $id
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return bool
     */
    public function delete(string $index, int|string $id): bool
    {
        return $this->searchRepository->delete($index, $id);
    }

    /**
     * @param string $index
     * @param string $query
     * @param int    $page
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @return SearchResultDto
     */
    public function search(string $index, string $query, int $page): SearchResultDto
    {
        if (! $page) {
            $page = 1;
        }

        $result = $this->searchRepository->search($index, $query, $page);

        return $this->getSearchResultDto($result);
    }

    /**
     * @param string $index
     * @param string $query
     * @param string $field
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @return SuggestCompletionResultDto
     */
    public function suggestCompletion(string $index, string $query, string $field): SuggestCompletionResultDto
    {
        $result = $this->searchRepository->suggestCompletion($index, $query, $field);

        return $this->getSuggestCompletionResultDto($result);
    }

    /**
     * @param array $result
     *
     * @return SearchResultDto
     */
    private function getSearchResultDto(array $result): SearchResultDto
    {
        $items = collect();

        foreach (collect($result['hits']['hits'] ?? [])->groupBy('_index') as $index => $docs) {
            $model = $this->getModelByIndex($index);

            $models = $model::find($docs->pluck('_id')
                ->toArray());

            foreach ($docs as $doc) {
                $items->push([
                    'score' => $doc['_score'],
                    'model' => $models->firstWhere('id', '=', $doc['_id']),
                ]);
            }
        }

        $searchResultDto = new SearchResultDto();

        foreach ($items->sortByDesc('score') as $item) {
            $searchResultItemDto        = new SearchResultItemDto();
            $searchResultItemDto->score = $item['score'];
            $searchResultItemDto->model = $item['model'];

            $searchResultDto->items[] = $searchResultItemDto;
        }

        $searchResultDto->total = $result['hits']['total']['value'] ?? 0;

        return $searchResultDto;
    }

    /**
     * @param array $result
     *
     * @return SuggestCompletionResultDto
     */
    private function getSuggestCompletionResultDto(array $result): SuggestCompletionResultDto
    {
        $items = collect();

        foreach (collect($result['suggest']['completion_suggest'][0]['options'] ?? [])->groupBy('_index') as $index => $options) {
            $model = $this->getModelByIndex($index);

            $models = $model::find($options->pluck('_id')
                ->toArray());

            foreach ($options as $option) {
                $items->push([
                    'text'  => $option['text'],
                    'model' => $models->firstWhere('id', '=', $option['_id']),
                ]);
            }
        }

        $suggestCompletionResultDto = new SuggestCompletionResultDto();

        foreach ($items as $item) {
            $suggestCompletionResultItemDto        = new SuggestCompletionResultItemDto();
            $suggestCompletionResultItemDto->text  = $item['text'];
            $suggestCompletionResultItemDto->model = $item['model'];

            $suggestCompletionResultDto->items[] = $suggestCompletionResultItemDto;
        }

        return $suggestCompletionResultDto;
    }

    /**
     * @param string $index
     *
     * @return null|string
     */
    private function getModelByIndex(string $index): ?string
    {
        return array_flip($this->indexMap)[$index] ?? null;
    }
}
