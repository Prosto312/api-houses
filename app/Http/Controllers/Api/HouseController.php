<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseSearchRequest;
use App\Services\HouseSearch\HouseSearchService;
use Illuminate\Support\Facades\Cache;
class HouseController extends Controller
{
    public function index(HouseSearchRequest $request)
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 20);
        $filters['per_page'] = $perPage;

        $cacheKey = $this->cacheKey($filters);
        $payload = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filters, $perPage) {
            $service = app(HouseSearchService::class);
            return $service->search($filters, $perPage);
        });

        return response()->json($payload);
    }

    private function cacheKey(array $filters): string
    {
        ksort($filters);
        return 'houses:search:' . sha1(json_encode($filters));
    }
}
