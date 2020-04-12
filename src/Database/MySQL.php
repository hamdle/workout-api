<?php
namespace Database;

use mysqli;

class MySQL
{
    protected static $mysql = null;

    protected static function connect()
    {
        if (is_null(self::$mysql))
        {
            self::$mysql = new mysqli(
                $_ENV['DB_HOST'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_NAME']
            );

            if (self::$mysql->connect_errno)
            {
                echo 'Database connection failed.';
                die();
            }
        }

        return self::$mysql;
    }

    /*
     * Run a sql query using OO version of mysqli
     * @param $query - a complete SQL query
     * @return $rows[] or $id or null
     */
    public static function run($query)
    {
        $db = self::connect();

        $rows = [];
        if ($results = $db->query($query))
        {
            if (is_bool($results)) {
                if ($results) {
                    return $db->insert_id;
                }
                return null;
            }
            if ($db->error) {
                \Utils\ErrorLog::print($db->error, 'database error');
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

    public static function close()
    {
        if (!is_null(self::$mysql))
        {
            self::$mysql->close();
        }
    }
}
