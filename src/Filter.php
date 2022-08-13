<?php

namespace ModelFilter;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;

/**
 * @mixin QueryBuilder
 */
abstract class Filter
{
    /**
     * The attributes that equal query is used by default.
     *
     * @var array
     */
    public $filterable = [];

    /**
     * Array of input to filter.
     *
     * @var array
     */
    protected $input;

    /**
     * @var QueryBuilder
     */
    protected $query;

    /**
     * ModelFilter constructor.
     *
     * @param QueryBuilder $query
     * @param array $input
     */
    public function __construct($query, array $input = [])
    {
        $this->query = $query;
        $this->input = array_filter($input);
    }

    public function handle()
    {
        foreach ($this->input as $key => $value) {
            $method = $this->getFilterMethod($key);
            if ($this->isCallable($method)) {
                $this->{$method}($value);
            } elseif ($this->isFilterable($key)) {
                if (is_array($value)) {
                    $this->query->whereIn($key, $value);
                } else {
                    $this->query->where($key, $value);
                }
            }
        }
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $rst = $this->query->{$method}(...$args);

        return $rst instanceof QueryBuilder ? $this : $rst;
    }

    public function isFilterable(string $key)
    {
        return in_array($key, $this->filterable);
    }

    public function isCallable(string $method)
    {
        return method_exists($this, $method) && !method_exists(Filter::class, $method);
    }

    public function getFilterMethod($key)
    {
        $method = str_replace('.', '', $key);

        return config('modelfilter.input_camel', true) ? Str::camel($method) : $method;
    }
}
