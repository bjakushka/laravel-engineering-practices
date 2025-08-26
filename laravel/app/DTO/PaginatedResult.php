<?php

namespace App\DTO;

/**
 * @template T
 */
readonly class PaginatedResult
{
    /**
     * @param T[] $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
    ) {}
}
