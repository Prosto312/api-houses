<?php

namespace Tests\Feature;

use App\Models\House;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_by_partial_name(): void
    {
        $this->createHouse([
            'name' => 'Victoria House',
            'price' => 400000,
            'bedrooms' => 4,
            'bathrooms' => 2,
            'storeys' => 2,
            'garages' => 2,
        ]);

        $this->createHouse([
            'name' => 'Other Home',
            'price' => 250000,
            'bedrooms' => 3,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $response = $this->getJson('/api/houses?name=Vic');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Victoria House');
    }

    public function test_search_by_exact_bedrooms(): void
    {
        $this->createHouse([
            'name' => 'Two Bed',
            'price' => 300000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $this->createHouse([
            'name' => 'Three Bed',
            'price' => 350000,
            'bedrooms' => 3,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $response = $this->getJson('/api/houses?bedrooms=2');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Two Bed');
    }

    public function test_search_by_price_range(): void
    {
        $this->createHouse([
            'name' => 'Low',
            'price' => 200000,
            'bedrooms' => 3,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $this->createHouse([
            'name' => 'Mid',
            'price' => 350000,
            'bedrooms' => 3,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $this->createHouse([
            'name' => 'High',
            'price' => 600000,
            'bedrooms' => 3,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $response = $this->getJson('/api/houses?price_from=300000&price_to=400000');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mid');
    }

    public function test_search_with_all_optional_params_empty(): void
    {
        $this->createHouse([
            'name' => 'One',
            'price' => 200000,
            'bedrooms' => 3,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 1,
        ]);

        $response = $this->getJson('/api/houses');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    private function createHouse(array $attributes): void
    {
        $now = now();
        House::query()->insert(array_merge([
            'name' => 'Default',
            'price' => 100000,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'storeys' => 1,
            'garages' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ], $attributes));
    }
}
