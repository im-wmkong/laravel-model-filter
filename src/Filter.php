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
     * @var QueryBuilder
     */
    protected $query;

    /**
     * Array of input to filter.
     *
     * @var array
     */
    protected $input;

    /**
     * ModelFilter constructor.
     *
     * @param QueryBuilder $query
     * @param array $input
     */
    public function __construct(QueryBuilder $query, array $input = [])
    {
        $this->query = $query;
        $this->input = array_filter($input);
    }

    /**
     * Execute the model filter.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->input as $key => $value) {
            $method = $this->getFilterMethod($key);
            if ($this->isCallable($method)) {
                $this->{$method}($value);
                continue;
            }

            if ($this->isFilterable($key)) {
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

    /**
     * This key is filterable
     *
     * @param string $key
     * @return boolean
     */
    public function isFilterable(string $key)
    {
        return in_array($key, $this->filterable);
    }

    /**
     * This method is callable
     *
     * @param string $method
     * @return boolean
     */
    public function isCallable(string $method)
    {
        return method_exists($this, $method) && !method_exists(Filter::class, $method);
    }

    /**
     * Get the filter method for the filter class.
     *
     * @param string $key
     * @return string
     */
    public function getFilterMethod($key)
    {
        $method = str_replace('.', '', $key);

        return config('modelfilter.input_camel', true) ? Str::camel($method) : $method;
    }
}
