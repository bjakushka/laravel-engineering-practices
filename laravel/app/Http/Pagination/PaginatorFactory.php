<?php

namespace App\Http\Pagination;

use App\DTO\PaginatedResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatorFactory
{
    /**
     * @param PaginatedResult<Model> $result The paginated result containing items and pagination details.
     * @param Request|null $request The HTTP request to extract URL and query parameters (optional).
     *
     * @return LengthAwarePaginator<int, Model> A LengthAwarePaginator instance for use in views.
     */
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
