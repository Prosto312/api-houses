<?php

namespace App\Services\HouseSearch;

interface HouseSearchDriver
{
    public function search(array $filters, int $perPage): array;
}
