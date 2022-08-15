<?php

namespace ModelFilter\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    /**
     * ModelFilter constructor.
     *
     * @param Builder $query
     * @param array $input
     */
    public function __construct(Builder $query, array $input = []);

    /**
     * Execute the model filter.
     *
     * @return void
     */
    public function handle();
}