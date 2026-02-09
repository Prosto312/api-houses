<?php

namespace App\Services\HouseSearch;

use App\Models\House;

class DatabaseHouseSearch implements HouseSearchDriver
{
    public function search(array $filters, int $perPage): array
    {
        $query = House::query();

        if (!empty($filters['name'])) {
            $name = $filters['name'];
            $query->where('name', 'like', "%{$name}%");
        }

        foreach (['bedrooms', 'bathrooms', 'storeys', 'garages'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, (int) $filters[$field]);
            }
        }

        $priceFrom = $filters['price_from'] ?? null;
        $priceTo = $filters['price_to'] ?? null;

        if ($priceFrom !== null && $priceTo !== null) {
            $query->whereBetween('price', [(int) $priceFrom, (int) $priceTo]);
        } elseif ($priceFrom !== null) {
            $query->where('price', '>=', (int) $priceFrom);
        } elseif ($priceTo !== null) {
            $query->where('price', '<=', (int) $priceTo);
        }

        $paginator = $query->paginate($perPage);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}
