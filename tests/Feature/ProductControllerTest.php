<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $cat = Category::query()->create(['name' => 'Electronics']);

        Product::query()->create([
            'name' => 'Alpha Phone',
            'price' => 100.00,
            'category_id' => $cat->id,
            'in_stock' => true,
            'rating' => 4.5,
        ]);

        Product::query()->create([
            'name' => 'Beta Book',
            'price' => 20.00,
            'category_id' => $cat->id,
            'in_stock' => false,
            'rating' => 3.0,
        ]);

        Product::query()->create([
            'name' => 'Gamma Phone XL',
            'price' => 200.00,
            'category_id' => $cat->id,
            'in_stock' => true,
            'rating' => 5.0,
        ]);
    }

    public function test_index_returns_paginated_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);

        $this->assertCount(3, $response->json('data'));
        $this->assertSame(3, $response->json('total'));

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'category_id',
                    'in_stock',
                    'rating',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_filter_q_substring_in_name(): void
    {
        $response = $this->getJson('/api/products?q=Phone');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Alpha Phone', $names);
        $this->assertContains('Gamma Phone XL', $names);
        $this->assertNotContains('Beta Book', $names);
    }

    public function test_filter_price_range(): void
    {
        $response = $this->getJson('/api/products?price_from=50&price_to=150');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('Alpha Phone', $response->json('data.0.name'));
    }

    public function test_filter_category_id(): void
    {
        $catId = Category::query()->first()->id;

        $response = $this->getJson('/api/products?category_id='.$catId);

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_filter_in_stock(): void
    {
        $response = $this->getJson('/api/products?in_stock=true');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        foreach ($response->json('data') as $row) {
            $this->assertTrue($row['in_stock']);
        }
    }

    public function test_filter_in_stock_false(): void
    {
        $response = $this->getJson('/api/products?in_stock=false');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertFalse($response->json('data.0.in_stock'));
    }

    public function test_filter_rating_from(): void
    {
        $response = $this->getJson('/api/products?rating_from=4.5');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Alpha Phone', $names);
        $this->assertContains('Gamma Phone XL', $names);
        $this->assertNotContains('Beta Book', $names);
    }

    public function test_sort_price_asc(): void
    {
        $response = $this->getJson('/api/products?sort=price_asc');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertSame(['Beta Book', 'Alpha Phone', 'Gamma Phone XL'], $names);
    }

    public function test_sort_price_desc(): void
    {
        $response = $this->getJson('/api/products?sort=price_desc');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertSame(['Gamma Phone XL', 'Alpha Phone', 'Beta Book'], $names);
    }

    public function test_sort_rating_desc(): void
    {
        $response = $this->getJson('/api/products?sort=rating_desc');

        $response->assertOk();
        $this->assertSame('Gamma Phone XL', $response->json('data.0.name'));
    }

    public function test_sort_newest(): void
    {
        $response = $this->getJson('/api/products?sort=newest');

        $response->assertOk();
        $this->assertSame('Gamma Phone XL', $response->json('data.0.name'));
    }

    public function test_invalid_sort_returns_validation_error(): void
    {
        $response = $this->getJson('/api/products?sort=invalid');

        $response->assertStatus(422);
    }

    public function test_per_page_limits_results(): void
    {
        $response = $this->getJson('/api/products?per_page=2');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        $this->assertSame(3, $response->json('total'));
    }
}
