<?php


namespace CoolRunner\Utils\Mixins;


use Illuminate\Database\Concerns\BuildsQueries;

class Eloquent
{
    public function getRawQuery()
    {
        /**
         * @var BuildsQueries|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
         */
        return function ($query = null) {
            return (new Builder())->getRawQuery()($query ?: $this);
        };

    }

    public function getRawQueryParts()
    {
        return function ($sql, $bindings, $no_quote = false) {
            return (new Builder())->getRawQueryParts()($sql, $bindings, $no_quote);
        };
    }
}
