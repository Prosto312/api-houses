<?php

namespace App\Services\HouseSearch;

class ElasticsearchHouseSearch implements HouseSearchDriver
{
    private DatabaseHouseSearch $fallback;

    public function __construct(DatabaseHouseSearch $fallback)
    {
        $this->fallback = $fallback;
    }

    public function search(array $filters, int $perPage): array
    {
        return $this->fallback->search($filters, $perPage);
    }
}
