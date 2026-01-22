<?php

namespace App\Contracts;

interface JobFetcher
{
    /**
     * Fetch jobs from the source.
     *
     * @return array
     */
    public function fetch(): array;
}