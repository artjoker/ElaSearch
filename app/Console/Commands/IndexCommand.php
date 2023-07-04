<?php

namespace App\Console\Commands;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Service\Search\SearchService;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Console\Command;

/**
 * Class IndexCommand.
 */
class IndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexing cards, posts and authors';

    /**
     * @param SearchService $searchService
     */
    public function __construct(
        private readonly SearchService $searchService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @return int
     */
    public function handle()
    {

        Product::each(function (Product $product) {
            $this->searchService->index($product);
        });

        $this->searchService->flush(Category::class);

        Category::each(function (Category $category) {
            $this->searchService->index($category);
        });

        $this->searchService->flush(Brand::class);

        Brand::each(function (Brand $brand) {
            $this->searchService->index($brand);
        });

        return Command::SUCCESS;
    }
}
