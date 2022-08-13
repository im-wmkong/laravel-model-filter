<?php

namespace EloquentFilter;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, array $input = [])
    {
        $filterClass = $this->getModelFilterClass();

        $modelFilter = new $filterClass($query, $input);

        $modelFilter->handle();
    }

    /**
     * Returns ModelFilter class to be instantiated.
     *
     * @return string
     */
    public function provideFilter()
    {
        return config('modelfilter.namespace', 'App\\Models\\Filters\\') . class_basename($this) . 'Filter';
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
