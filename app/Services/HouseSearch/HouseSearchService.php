<?php

namespace App\Services\HouseSearch;

use App\Services\HouseSearch\ElasticsearchHouseSearch;

class HouseSearchService
{
    private HouseSearchDriver $driver;

    public function __construct()
    {
        $driverName = config('services.search.driver', 'database');
        $this->driver = $driverName === 'elasticsearch'
            ? app(ElasticsearchHouseSearch::class)
            : app(DatabaseHouseSearch::class);
    }

    public function search(array $filters, int $perPage): array
    {
        return $this->driver->search($filters, $perPage);
    }
}
