<?php

namespace App\Console\Commands;

use App\Models\Creator;
use Faker\Factory;
use Illuminate\Console\Command;
use Solarium\QueryType\Update\Query\Document;

class SolrCreators extends Command
{
    const BATCH = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:creators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update creators in solr.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function deleteAll()
    {
        $client = solr_client('creators');

        $update = $client->createUpdate();

// add the delete query and a commit command to the update query
        $update->addDeleteQuery('*:*');
        $update->addCommit();

        $result = $client->update($update);

        $this->info("Update query executed\n");
        echo "Query status: " . $result->getStatus(). "\n";
        echo "Query time: " . $result->getQueryTime();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $this->deleteAll();

        $client = solr_client('creators');

        $creators = Creator::cursor();

        ($bar = $this->output->createProgressBar($creators->count()))->start();

        /** @var Creator $creator */
        foreach ($creators as $creator) {

            $creator->load(['info', 'image', 'categories', 'sources']);

            //dd($creator->toArray());
            $update = $client->createUpdate();

            /** @var Document $document */
            $document = $update->createDocument(
                $creator->toArray()
            );
//            $document->addField();
            $document->id = $creator->id;
            $document->setKey('_id', $creator->id);
            $document->setFieldModifier('name', $document::MODIFIER_SET);

            $update->addDocument($document);
            $update->addCommit();
            $client->update($update);


            $bar->advance();
            //exit;
        }

        $bar->finish();
        $this->info("\nDone.");
        return 0;
    }
}
