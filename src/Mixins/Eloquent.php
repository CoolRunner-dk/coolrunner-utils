<?php


namespace CoolRunner\Utils\Mixins;


use Illuminate\Database\Concerns\BuildsQueries;

class Eloquent
{
    public function getRawQuery() {
        /**
         * @var BuildsQueries|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
         */
        return function ($query = null) {
            return (new Builder())->getRawQuery()($query ?: $this);
        };

    }
}
