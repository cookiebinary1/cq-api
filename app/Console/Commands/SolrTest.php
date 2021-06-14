<?php

namespace App\Console\Commands;

use App\Models\Collab;
use Illuminate\Console\Command;
use Solarium\QueryType\Update\Query\Document;
use Faker\Factory;

class SolrTest extends Command
{
    const NUMBER = 10000000;
    const BATCH = 100;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert 1,000,000 test records into Solr database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = solr_client('mytest');

        ($bar = $this->output->createProgressBar(self::NUMBER))->start();

        // create a new document
        for ($o = 0; $o < self::NUMBER / self::BATCH; $o++) {

            $update = $client->createUpdate();

            for ($i = 0; $i < self::BATCH; $i++) {
                /** @var Document $doc1 */
                $doc1 = $update->createDocument([
                    'name'  => Factory::create()->name,
                    'email' => Factory::create()->email,
                    'price' => 364 * $i,
                ]);

                $update->addDocument($doc1);

                $bar->advance();
            }

            $update->addCommit();
            $client->update($update);
        }

        $bar->finish();
        $this->info("\nDone.");
        return 0;
    }
}
