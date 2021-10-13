<?php


namespace CoolRunner\Utils\Mixins;


use Illuminate\Database\Concerns\BuildsQueries;

class Builder
{

    public function getRawQuery() {
        /**
         * @var BuildsQueries|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
         */
        return function ($query) : string {
            if (env('APP_ENV') === 'production') {
                throw new \Exception('builder_get_raw_query should not be used in production!');
            }
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $sql      = $query->toSql();
            $bindings = $query->getBindings();

            return (new Builder())->getRawQueryParts()($sql, $bindings);
        };
    }

    public function getRawQueryParts() {
        return function ($sql, $bindings, $no_quote = false) : string {
            if (env('APP_ENV') === 'production') {
                throw new \Exception('parts_get_raw_query should not be used in production!');
            }

            foreach ($bindings as $binding) {
                if (!preg_match('/^[0-9]$/', $binding)) {
                    $binding = $no_quote ? $binding : "'$binding'";
                }
                $sql = \Illuminate\Support\Str::replaceFirst('?', "$binding", $sql);
            }

            return $sql;
        };
    }
}
