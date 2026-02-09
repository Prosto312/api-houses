<?php

namespace App\Console\Commands;

use App\Models\House;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class HousesReindex extends Command
{
    protected $signature = 'houses:reindex';

    protected $description = 'Reindex houses into Elasticsearch';

    public function handle()
    {
        $host = config('services.search.elasticsearch_host', 'http://localhost:9200');
        $index = config('services.search.elasticsearch_index', 'houses');

        $client = ClientBuilder::create()->setHosts([$host])->build();

        if ($client->indices()->exists(['index' => $index])->asBool()) {
            $client->indices()->delete(['index' => $index]);
        }

        $client->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => ['type' => 'keyword'],
                            ],
                        ],
                        'bedrooms' => ['type' => 'integer'],
                        'bathrooms' => ['type' => 'integer'],
                        'storeys' => ['type' => 'integer'],
                        'garages' => ['type' => 'integer'],
                        'price' => ['type' => 'integer'],
                    ],
                ],
            ],
        ]);

        House::query()->orderBy('id')->chunk(500, function ($houses) use ($client, $index) {
            $params = ['body' => []];

            foreach ($houses as $house) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $index,
                        '_id' => $house->id,
                    ],
                ];
                $params['body'][] = [
                    'id' => $house->id,
                    'name' => $house->name,
                    'bedrooms' => $house->bedrooms,
                    'bathrooms' => $house->bathrooms,
                    'storeys' => $house->storeys,
                    'garages' => $house->garages,
                    'price' => $house->price,
                ];
            }

            if ($params['body']) {
                $client->bulk($params);
            }
        });

        $client->indices()->refresh(['index' => $index]);

        $this->info('Elasticsearch index refreshed');
    }
}
