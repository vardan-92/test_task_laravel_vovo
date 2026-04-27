<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexProductsRequest;
use App\Models\Product;

class ProductController extends Controller
{
    private const DEFAULT_PER_PAGE = 15;

    public function index(IndexProductsRequest $request)
    {
        $filters = $request->filters();
        $perPage = $filters['per_page'] ?? self::DEFAULT_PER_PAGE;

        return Product::query()
            ->filter($filters)
            ->sort($filters['sort'] ?? 'newest')
            ->paginate($perPage)
            ->withQueryString();
    }
}
