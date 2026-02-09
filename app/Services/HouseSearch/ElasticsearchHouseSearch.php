<?php

namespace App\Services\HouseSearch;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchHouseSearch implements HouseSearchDriver
{
    private DatabaseHouseSearch $fallback;

    public function __construct(DatabaseHouseSearch $fallback)
    {
        $this->fallback = $fallback;
    }

    public function search(array $filters, int $perPage): array
    {
        try {
            $client = $this->client();
            if (!$client->ping()->asBool()) {
                return $this->fallback->search($filters, $perPage);
            }

            $index = config('services.search.elasticsearch_index', 'houses');
            $page = (int) ($filters['page'] ?? 1);
            $from = max(0, ($page - 1) * $perPage);

            $must = [];
            $filter = [];

            if (!empty($filters['name'])) {
                $must[] = [
                    'match_phrase_prefix' => [
                        'name' => [
                            'query' => $filters['name'],
                        ],
                    ],
                ];
            }

            foreach (['bedrooms', 'bathrooms', 'storeys', 'garages'] as $field) {
                if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                    $filter[] = ['term' => [$field => (int) $filters[$field]]];
                }
            }

            $priceFrom = $filters['price_from'] ?? null;
            $priceTo = $filters['price_to'] ?? null;
            if ($priceFrom !== null || $priceTo !== null) {
                $range = [];
                if ($priceFrom !== null) {
                    $range['gte'] = (int) $priceFrom;
                }
                if ($priceTo !== null) {
                    $range['lte'] = (int) $priceTo;
                }
                $filter[] = ['range' => ['price' => $range]];
            }

            $body = [
                'from' => $from,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ])->asArray();

            $hits = $response['hits']['hits'] ?? [];
            $total = $response['hits']['total']['value'] ?? 0;

            $data = array_map(function (array $hit) {
                return $hit['_source'] ?? [];
            }, $hits);

            $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 0;

            return [
                'data' => $data,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                ],
            ];
        } catch (\Throwable $e) {
            return $this->fallback->search($filters, $perPage);
        }
    }

    private function client()
    {
        $host = config('services.search.elasticsearch_host', 'http://localhost:9200');
        return ClientBuilder::create()->setHosts([$host])->build();
    }
}
