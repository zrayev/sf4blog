<?php

namespace App\DTO;

class SearchDTO
{
    /**
     * @var string
     */
    private $query;

    /**
     * @return string
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery(?string $query): void
    {
        $this->query = trim($query);
    }
}
