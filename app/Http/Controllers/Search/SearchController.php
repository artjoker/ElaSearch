<?php

declare(strict_types=1);

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Core\Constants;
use App\Http\Requests\SearchRequest;
use App\Service\Search\SearchService;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class SearchController.
 */
class SearchController extends Controller
{
    /**
     * @param SearchService $searchService
     */
    public function __construct(
        private readonly SearchService $searchService,
    ) {
    }

    /**
     * @param SearchRequest $searchRequest
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @return Application|Factory|string|View
     */
    public function search(SearchRequest $searchRequest): Factory|View|Application|string
    {
        $searchResultDto = $this->searchService->search(
            '_all',
            $searchRequest->get('query', ''),
            (int)$searchRequest->get('page', 1),
        );

        $pagination = new LengthAwarePaginator(
            $searchResultDto->getModels(),
            $searchResultDto->total,
            Constants::SEARCH_PER_PAGE,
        );

        if (request()->ajax()) {
            $resultHtml = '';
            foreach ($pagination->items() as $item) {
                $resultHtml .= view('frontend.sections.search-item', [
                    'item' => $item,
                ])->render();
            }

            return $resultHtml;
        }

        $viewData['pagination'] = $pagination;

        return view('frontend.pages.search')
            ->with($viewData);
    }

    /**
     * @param SearchRequest $searchRequest
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @return array
     */
    public function suggest(SearchRequest $searchRequest): array
    {
        $suggestCompletionResultDto = $this->searchService->suggestCompletion(
            '_all',
            $searchRequest->get('query', ''),
            'title.completion',
        );

        return $suggestCompletionResultDto->getSuggest();
    }
}
