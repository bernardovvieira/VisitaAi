<?php

namespace App\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;

/**
 * Builder do modelo User: traduz where('login', ...) para where('use_email', ...).
 * Evita "Unknown column 'login'" quando Fortify/guard usam a chave 'login' (ex.: config em cache).
 */
class UserBuilder extends Builder
{
    /**
     * @param  \Closure|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column === 'login') {
            $column = 'use_email';
        }
        $args = func_get_args();
        $args[0] = $column;

        return parent::where(...$args);
    }
}
