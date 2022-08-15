<?php

namespace ModelFilter\Traits;

use Illuminate\Database\Eloquent\Builder;
use ModelFilter\Contracts\Filter as FilterContracts;

trait Filterable
{
    /**
     * add filter method for query builder
     *
     * @param Builder $query
     * @param array $input
     * @return void
     */
    public function scopeFilter(Builder $query, array $input = [])
    {
        $filterClass = $this->getModelFilterClass();

        if (!class_exists($filterClass)) {
            return;
        }

        $modelFilter = new $filterClass($query, $input);

        if (!($modelFilter instanceof FilterContracts)) {
            return;
        }

        $modelFilter->handle();
    }

    /**
     * Returns ModelFilter class to be instantiated.
     *
     * @return string
     */
    public function provideFilter()
    {
        return config('modelfilter.namespace', 'App\\Filters') . '\\' . class_basename($this) . 'Filter';
    }

    /**
     * Returns the ModelFilter for the current model.
     *
     * @return string
     */
    public function getModelFilterClass()
    {
        return method_exists($this, 'modelFilter') ? $this->modelFilter() : $this->provideFilter();
    }
}
