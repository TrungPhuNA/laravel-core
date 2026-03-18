<?php

namespace Tests\Unit;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Http\Request;
use Tests\TestCase;

final class ApiQueryParamsTest extends TestCase
{
    public function test_it_parses_filter_sort_include_and_pagination(): void
    {
        config([
            'core.api.pagination.default_per_page' => 20,
            'core.api.pagination.max_per_page' => 50,
            'core.api.pagination.page_param' => 'page',
            'core.api.pagination.per_page_param' => 'per_page',
        ]);

        $request = Request::create('/api/v1/products', 'GET', [
            'filter' => [
                'name' => 'Core',
                'status' => 'active,inactive',
            ],
            'include' => 'category,brand',
            'sort' => 'name,-created_at',
            'page' => 2,
            'per_page' => 999,
        ]);

        $params = ApiQueryParams::fromRequest($request);

        $this->assertSame(2, $params->page);
        $this->assertSame(50, $params->perPage);
        $this->assertSame(['category', 'brand'], $params->includes);
        $this->assertSame(['name', '-created_at'], $params->sorts);
        $this->assertSame('Core', $params->filters['name']);
        $this->assertSame('active,inactive', $params->filters['status']);
    }
}

