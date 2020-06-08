<?php

namespace App\Console\Commands;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use App\Airtable;
use App\Blog;
use App\User;
use App\Util;
use Illuminate\Console\Command;
use JsonStreamingParser\Listener\JsonListener;

class AlgoliaIndex extends Command
{

    /** @var SearchIndex */
    protected $index;
    protected $currentTweetNumber = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algolia:index {--limit=} {--v} {--tables=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send data to algolia index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function _limit()
    {
        return $this->option('limit') ? $this->option('limit') : 5;
    }

    protected function _tables()
    {
        return $this->option('tables') ? explode(',', $this->option('tables')) : null;
    }

    protected function shouldIndex($table)
    {
        if (!$this->_tables()) {
            return true;
        }

        $tables = $this->_tables();
        return in_array($table, $tables);
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     * @throws \Exception
     */
    public function handle()
    {
        $params = array(
            "sort"       => [['field' => 'Published', 'direction' => "desc"]],
            "maxRecords" => $this->_limit(),
        );

        $client = SearchClient::create(Util::algoliaAppId(), Util::algoliaPrivateKey());
        $this->index = $client->initIndex('all');
        $this->info("Updating Algolia search index (limit: " . $this->_limit() . ")");

        if ($this->shouldIndex('pages')) {
            $this->_indexPages();
        }

        if ($this->shouldIndex('tweets')) {
            $this->_indexTweets();
        }

        return;
    }

    /**
     * @param Blog $blog
     *
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     */
    protected function _indexRecords($records, $name)
    {
        $this->info(count($records) . " $name");
        foreach ($records as $record) {
            /** @var Airtable $record */
            $this->info($record->searchTitle() . " - " . $record->searchIndexId());
            $data = $record->toSearchIndexArray();

            if ($this->option('v')) {
                $this->info(" - Data:");
                foreach ($data as $key => $val) {
                    $this->info("    - $key: $val");
                }
            }
            $this->index->saveObjects([$data], [
                'objectIDKey' => 'object_id',
            ]);
        }
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     */
    protected function _indexPages()
    {
        $pages = [
            [
                'url'  => '/',
                'name' => 'Home',
            ],
        ];

        $this->info("Indexing " . count($pages) . " pages");
        foreach ($pages as $page) {
            $page['object_id'] = 'page_' . $page['url'];
            $page['search_title'] = "Page: " . $page['name'];
            $page['type'] = 'page';
            $page['public'] = true;

            $this->info(" - " . $page['search_title'] . " - " . $page['object_id']);

            $this->index->saveObjects([$page], [
                'objectIDKey' => 'object_id',
            ]);
        }
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     * @throws \Exception
     */
    protected function _indexTweets()
    {
        $this->info("Indexing tweets");

        try {
            $testfile = storage_path('app/tweet.js');
        } catch (\Exception $e) {
            $this->error("Didn't find file: " . storage_path('app/tweet.js'));
        }

        $listener = new \App\JsonListener(function($data) {
            $i = $this->currentTweetNumber;
            $this->info(" - Tweet ({$data['id']}) #$i: " . $data['full_text']);

            $object = [];
            $object['object_id'] = 'tweet_' . $data['id'];
            $object['url'] = "https://twitter.com/" . Util::twitterUsername() . '/statuses/' . $data['id'];
            $object['search_title'] = "Tweet: " . $data['full_text'];
            $object['type'] = 'tweet';
            $object['public'] = true;

            $this->index->saveObjects([$object], [
                'objectIDKey' => 'object_id',
            ]);

            $this->currentTweetNumber++;
            if ($this->currentTweetNumber > $this->_limit()) {
                die();
            }
        });

        $stream = fopen($testfile, 'r');
        try {
            $parser = new \JsonStreamingParser\Parser($stream, $listener);
            $parser->parse();
            fclose($stream);
        } catch (\Exception $e) {
            fclose($stream);
            throw $e;
        }

    }
}
