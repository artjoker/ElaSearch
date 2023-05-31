<?php

declare(strict_types=1);

namespace App\Repositories\Search;

use App\Http\Core\Constants;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class SearchRepository.
 */
class SearchRepository
{
    /**
     * @param Client $elasticsearch
     */
    public function __construct(
        private readonly Client $elasticsearch
    ) {
    }

    /**
     * @param string $index
     * @param array  $settings
     * @param array  $mappings
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return bool
     */
    public function createIndex(string $index, array $settings, array $mappings): bool
    {
        if (! $this->elasticsearch->indices()
            ->exists(['index' => $index])
            ->asBool()
        ) {
            return $this->elasticsearch->indices()
                ->create([
                    'index' => $index,
                    'body'  => [
                        'settings' => $settings,
                        'mappings' => $mappings,
                    ],
                ])
                ->asBool();
        }

        return true;
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
        if ($this->elasticsearch->indices()
            ->exists(['index' => $index])
            ->asBool()
        ) {
            return $this->elasticsearch->indices()
                ->delete([
                    'index' => $index,
                ])
                ->asBool();
        }

        return true;
    }

    /**
     * @param string     $index
     * @param int|string $id
     * @param array      $body
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return array
     */
    public function index(string $index, int|string $id, array $body): array
    {
        return $this->elasticsearch->index([
            'index' => $index,
            'id'    => $id,
            'body'  => $body,
        ])->asArray();
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
        return $this->elasticsearch->delete([
            'index' => $index,
            'id'    => $id,
        ])->asBool();
    }

    /**
     * @param string $index
     * @param string $query
     * @param int    $page
     * @param array  $fields
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @return array
     */
    public function search(string $index, string $query, int $page, array $fields = []): array
    {
        $params = [
            '_source' => false,
            'from'    => ($page - 1) * Constants::SEARCH_PER_PAGE,
            'size'    => Constants::SEARCH_PER_PAGE,
            'body'    => [
                'indices_boost' => [
                    ['cards' => 1.5],
                    ['posts' => 1.3],
                    ['users' => 1],
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            'simple_query_string' => [
                                'query'  => $query,
                                'fields' => $fields ?: [
                                    'title^3',
                                    'content',
                                ],
                            ],
                        ],
                        'filter' => [
                            'term' => [
                                'published' => 'yes',
                            ],
                        ],
                    ],
                ],
                'min_score' => 2,
            ],
        ];

        if ($index) {
            $params['index'] = $index;
        }

        return $this->elasticsearch->search($params)
            ->asArray();
    }

    /**
     * @param string $index
     * @param string $query
     * @param string $field
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @return array
     */
    public function suggestCompletion(string $index, string $query, string $field): array
    {
        $params = [
            'index' => $index,
            'body'  => [
                'suggest' => [
                    'completion_suggest' => [
                        'prefix'     => $query,
                        'completion' => [
                            'field'   => $field,
                            'context' => [
                                'published' => 'yes',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->elasticsearch->search($params)
            ->asArray();
    }
}
