<?php

namespace App\DTO;

/**
 * @template-covariant TItem
 */
readonly class PaginatedResult
{
    /**
     * @param TItem[] $items
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
