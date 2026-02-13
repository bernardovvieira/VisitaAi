<?php

namespace App\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;

/**
 * Builder do modelo User: traduz where('login', ...) para where('use_email', ...).
 * Evita "Unknown column 'login'" quando Fortify/guard usam a chave 'login' (ex.: confirm-password / 2FA).
 */
class UserBuilder extends Builder
{
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $column = $this->loginColumnToUseEmail($column);
        $args = func_get_args();
        $args[0] = $column;

        return parent::where(...$args);
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        $column = $this->loginColumnToUseEmail($column);
        $args = func_get_args();
        $args[0] = $column;

        return parent::orWhere(...$args);
    }

    /**
     * @param  mixed  $column
     * @return mixed
     */
    private function loginColumnToUseEmail($column)
    {
        return $column === 'login' ? 'use_email' : $column;
    }
}
