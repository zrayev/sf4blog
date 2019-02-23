<?php

namespace App\Pagination;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaginatedCollection
{
    /**
     * @Groups({"paginatedCollection:show"})
     */
    public $items;
    /**
     * @Groups({"paginatedCollection:show"})
     */
    public $total;
    /**
     * @Groups({"paginatedCollection:show"})
     */
    public $count;
    /**
     * @Groups({"paginatedCollection:show"})
     * @SerializedName("_links")
     */
    public $links = [];

    public function __construct(array $items, $totalItems)
    {
        $this->items = $items;
        $this->total = $totalItems;
        $this->count = \count($items);
    }

    /**
     * @param $ref
     * @param $url
     */
    public function addLink($ref, $url): void
    {
        $this->links[$ref] = $url;
    }
}
