<?php

namespace App\Http\Pagination;

use App\DTO\PaginatedResult;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatorFactory
{
    public static function fromPaginatedResult(PaginatedResult $result, ?Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: $result->items,
            total: $result->total,
            perPage: $result->perPage,
            currentPage: $result->currentPage,
            options: [
                'path' => $request?->url() ?? '',
                'query' => $request?->query() ?? [],
            ]
        );
    }
}
