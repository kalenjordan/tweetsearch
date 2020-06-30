<?php

namespace App\Console\Commands;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use App\Airtable;
use App\Blog;
use App\Twitter;
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
        $client = SearchClient::create(Util::algoliaAppId(), Util::algoliaPrivateKey());
        $this->index = $client->initIndex('all');
        $this->info("Updating Algolia search index (limit: " . $this->_limit() . ")");

        if ($this->shouldIndex('pages')) {
            $this->_indexPages();
        }

        if ($this->shouldIndex('twitter-download')) {
            $this->currentTweetNumber = 1;
            $this->_indexDownloadedTwitterData();
        }

        $this->currentTweetNumber = 1;
        $this->_indexOlderTweets();

        $this->currentTweetNumber = 1;
        $this->_indexNewerTweets();

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
    protected function _indexOlderTweets()
    {
        if (Util::lastTweetId()) {
            $this->info(" - Incremental: getting tweets prior to: " . Util::lastTweetId());
            $tweets = Twitter::tweets(Util::twitterUsername(), $this->_limit(), Util::lastTweetId());
        } else {
            $tweets = Twitter::tweets(Util::twitterUsername(), $this->_limit());
        }

        $this->info("Indexing " . count($tweets) . " tweets");
        foreach ($tweets as $tweet) {
            $object = [];
            $object['object_id'] = 'tweet_' . $tweet->id;
            $object['url'] = "https://twitter.com/" . Util::twitterUsername() . '/status/' . $tweet->id;
            $object['search_title'] = "Tweet: " . $tweet->text;
            $object['type'] = 'tweet';
            $object['created_at'] = $tweet->created_at;
            $object['public'] = true;

            $this->infoTweet($object);

            $this->index->saveObjects([$object], [
                'objectIDKey' => 'object_id',
            ]);

            $this->currentTweetNumber++;
        }

        if (isset($tweet)) {
            $this->info("Saving last tweet ID: " . $tweet->id);
            $this->saveLastTweetId($tweet->id);
        }
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     * @throws \Exception
     */
    protected function _indexNewerTweets()
    {
        $this->info("Indexing newer tweets");
        $client = SearchClient::create(Util::algoliaAppId(), Util::algoliaPrivateKey());
        $index = $client->initIndex('all');

        $index->setSettings([
            'ranking' => [
                'desc(object_id)',
            ]
        ]);

        $results = $index->search('', [
            'hitsPerPage' => 1,
        ]);
        $firstResult = $results['hits'][0];
        $tweetId = substr($firstResult['object_id'], 6);

        $this->info(" - Incremental: getting tweets after : " . $tweetId);
        $tweets = Twitter::newTweets(Util::twitterUsername(), $this->_limit(), $tweetId);

        $this->info("Indexing " . count($tweets) . " tweets");
        foreach ($tweets as $tweet) {
            $object = [];
            $object['object_id'] = 'tweet_' . $tweet->id;
            $object['url'] = "https://twitter.com/" . Util::twitterUsername() . '/status/' . $tweet->id;
            $object['search_title'] = "Tweet: " . $tweet->text;
            $object['type'] = 'tweet';
            $object['created_at'] = $tweet->created_at;
            $object['public'] = true;

            $this->infoTweet($object);

            $this->index->saveObjects([$object], [
                'objectIDKey' => 'object_id',
            ]);

            $this->currentTweetNumber++;
        }

        if (isset($tweet)) {
            $this->info("Saving last tweet ID: " . $tweet->id);
            $this->saveLastTweetId($tweet->id);
        }
    }

    protected function saveLastTweetId($tweetId)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $updated = preg_replace('/TWITTER_LAST_TWEET_ID=\d*/', 'TWITTER_LAST_TWEET_ID=' . $tweetId, file_get_contents($path));
            file_put_contents($path, $updated);
        }
    }

    /**
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     * @throws \Exception
     */
    protected function _indexDownloadedTwitterData()
    {
        $this->info("Indexing downloaded twitter tweets");

        $file = storage_path('app/tweet.js');
        if (!file_exists($file)) {
            $this->warn("Didn't find tweet.js file - skipping: " . storage_path('app/tweet.js'));
            return;
        }

        $listener = new \App\JsonListener(function ($data) {
            $object = [];
            $object['object_id'] = 'tweet_' . $data['id'];
            $object['url'] = "https://twitter.com/" . Util::twitterUsername() . '/status/' . $data['id'];
            $object['search_title'] = "Tweet: " . $data['full_text'];
            $object['type'] = 'tweet';
            $object['public'] = true;

            $this->infoTweet($object);

            $this->index->saveObjects([$object], [
                'objectIDKey' => 'object_id',
            ]);

            $this->currentTweetNumber++;
            if ($this->currentTweetNumber > $this->_limit()) {
                die();
            }
        });

        $stream = fopen($file, 'r');
        try {
            $parser = new \JsonStreamingParser\Parser($stream, $listener);
            $parser->parse();
            fclose($stream);
        } catch (\Exception $e) {
            fclose($stream);
            throw $e;
        }

    }

    protected function infoTweet($object)
    {
        $i = $this->currentTweetNumber;
        $this->info(" - #$i ({$object['object_id']}) ({$object['created_at']}): " . $object['search_title']);
    }
}
