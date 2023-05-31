<?php

namespace App\Console\Commands\Search;

use App\Models\Card\Card;
use App\Models\Post\Post;
use App\Models\User;
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
        $this->searchService->flush(Post::class);

        Post::each(function (Post $post) {
            $this->searchService->index($post);
        });

        $this->searchService->flush(Card::class);

        Card::each(function (Card $card) {
            $this->searchService->index($card);
        });

        $this->searchService->flush(User::class);

        User::each(function (User $user) {
            $this->searchService->index($user);
        });

        return Command::SUCCESS;
    }
}
