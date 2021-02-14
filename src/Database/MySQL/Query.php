<?php

/*
 * Database/MySQL/Query.php: run queries using mysql connection
 *
 * Copyright (C) 2021 Eric Marty
 */

namespace Database\MySQL;

class Query
{
    /*
     * Run a select query.
     *
     * @param $query - a complete SQL query
     * @return see Database\MySQL\Query::run()
     */
    public static function select($query)
    {
        return self::run($query);
    }

    /*
     * Run a sql query using OO version of mysqli.
     *
     * @param $query - a complete SQL query
     * @return $rows[] | $id | null
     */
    public static function run($query)
    {
        $db = Connection::mysql();

        $rows = [];
        if ($results = $db->query($query))
        {
            if (is_bool($results))
            {
                if ($results)
                    return $db->insert_id;

                return null;
            }
            if ($db->error)
            {
                \Utils\Logger::error($db->error, 'A database error has occured.');
                return null;
            }
            while ($row = $results->fetch_assoc())
            {
                $rows[] = $row;
            }

            $results->free();
        }

        return $rows;
    }

    public function execute($args, $table)
    {
        $query = $this->buildFilterBy($args, $table);
        return $this->run($query);
    }

    public function get($id, $table)
    {
        $query = "select * from {$table} where id = {$id}";
        return $this->run($query);
    }

    public function add($args, $table)
    {
        $query = "insert into {$table} (";
        $values = "";

        foreach ($args as $key => $value)
        {
            $query .= "`{$key}`,"; 
            $values .= "'{$value}',";
        }
        $query = substr($query, 0, -1);
        $values = substr($values, 0, -1);
        $query .= ") values ({$values})";

        return $this->run($query);
    }

    private function buildFilterBy($args, $table)
    {
        $query = "select * from {$table}";

        if (count($args) > 0)
            $query .= " where";

        $count = 1;
        foreach ($args as $key => $value)
        {
            if (count($args) === 1)
                $query .= " `{$key}` = '{$value}'";
            else if ($count < count($args))
                $query .= " `{$key}` = '{$value}' and";
            else
                $query .= " `{$key}` = '{$value}'";

            $count++;
        }

        return $query;
    }
}
