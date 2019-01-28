<?php

namespace App\Library\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\MySqlGrammar as Grammar;
use App\Library\Database\Query\MySqlBuilder as Builder;

class MySqlGrammar extends Grammar
{
    /**
     * Compile a replace into statement into SQL.
     *
     * @link https://dev.mysql.com/doc/refman/5.5/en/replace.html
     *
     * @param  \App\Library\Database\Query\MySqlBuilder  $query
     * @param  array  $values
     * @return string
     */
    public function compileReplace(Builder $query, array $values)
    {
        // Essentially we will force every insert to be treated as a batch insert which
        // simply makes creating the SQL easier for us since we can utilize the same
        // basic routine regardless of an amount of records given to us to insert.
        $table = $this->wrapTable($query->from);

        if (! is_array(reset($values))) {
            $values = [$values];
        }

        $columns = $this->columnize(array_keys(reset($values)));

        // We need to build a list of parameter place-holders of values that are bound
        // to the query. Each insert should have the exact same amount of parameter
        // bindings so we will loop through the record and parameterize them all.
        $parameters = [];

        foreach ($values as $record) {
            $parameters[] = '('.$this->parameterize($record).')';
        }

        $parameters = implode(', ', $parameters);

        return "replace into $table ($columns) values $parameters";
    }

    /**
     * Compile an insert ignore statement into SQL.
     *
     * @link https://dev.mysql.com/doc/refman/5.5/en/insert.html
     *
     * @param  \App\Library\Database\Query\MySqlBuilder  $query
     * @param  array  $values
     * @return string
     */
    public function compileInsertIgnore(Builder $query, array $values)
    {
        // Essentially we will force every insert to be treated as a batch insert which
        // simply makes creating the SQL easier for us since we can utilize the same
        // basic routine regardless of an amount of records given to us to insert.
        $table = $this->wrapTable($query->from);

        if (! is_array(reset($values))) {
            $values = [$values];
        }

        $columns = $this->columnize(array_keys(reset($values)));

        // We need to build a list of parameter place-holders of values that are bound
        // to the query. Each insert should have the exact same amount of parameter
        // bindings so we will loop through the record and parameterize them all.
        $parameters = [];

        foreach ($values as $record) {
            $parameters[] = '('.$this->parameterize($record).')';
        }

        $parameters = implode(', ', $parameters);

        return "insert ignore into $table ($columns) values $parameters";
    }

    /**
     * Compile an insert update statement into SQL.
     *
     * @link https://dev.mysql.com/doc/refman/8.0/en/insert-on-duplicate.html
     *
     * @param  \App\Library\Database\Query\MySqlBuilder  $query
     * @param  array  $values
     * @return string
     */
    public function compileInsertUpdate(Builder $query, array $values)
    {
        // Essentially we will force every insert to be treated as a batch insert which
        // simply makes creating the SQL easier for us since we can utilize the same
        // basic routine regardless of an amount of records given to us to insert.
        $table = $this->wrapTable($query->from);

        // Each one of the columns in the update statements needs to be wrapped in the
        // keyword identifiers, also a place-holder needs to be created for each of
        // the values in the list of bindings so we can make the sets statements.
        $columns = [];

        $values = reset($values);

        foreach ($values as $key => $value) {
            $columns[] = $this->wrap($key).' = '.$this->parameter($value);
        }
        $columns = implode(', ', $columns);

        return "insert into $table set $columns ON DUPLICATE KEY UPDATE $columns";
    }
}
