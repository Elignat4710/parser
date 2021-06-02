<?php

namespace App\Classes;

use PDO;
use PDOException;

/**
 * Class for MySQL
 */
class MySql
{
    // A variable for PDO class instance
    protected $pdo;

    /**
     * A builder with the connection to MYSQL
     */
    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host=".env('MYSQL_HOST', '127.0.0.1').";dbname=".env('MYSQL_DBNAME', 'jove').";charset=utf8",
                env('MYSQL_USER', 'root'),
                env('MYSQL_PASSWORD', '')
            );
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Breaking connection to the db
     */
    public function __destruct()
    {
        $this->pdo = null;
    }

    /**
     * Inserting the records
     *
     * @param  string $table
     * @param  $param
     * @return array
     */
    public function insert($table, $param)
    {
        $param['create_at'] = date('Y-m-d H:i:s');
        
        $sql = sprintf(
            'insert into %s (%s) values (%s)',
            $table,
            implode(', ', array_keys($param)),
            ':' . implode(', :', array_keys($param))
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($param);
            return [$this->pdo->lastInsertId(), 'create'];
        } catch (\Exception $exception) {
            var_dump($sql);
            die($exception->getMessage());
        }
    }

    /**
     * Updating the records
     *
     * @param  string $table
     * @param  $param
     * @return array
     */
    public function update($table, $param)
    {
        $set = '';
        $x = 1;

        $param['update_at'] = date('Y-m-d H:i:s');

        foreach ($param as $field => $value) {
            $set .= "{$field} = :$field";
            if ($x < count($param)) {
                $set .= ',';
            }
            $x++;
        }

        $query = sprintf(
            "UPDATE %s SET %s WHERE link = '%s'",
            $table,
            $set,
            $param['link']
        );
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($param);
            return [$this->searchForLink($table, $param['link']), 'update'];
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * Updating the records
     *
     * @param  string $table
     * @param  $param
     * @return array
     */
    public function updateOrCreate($table, $param)
    {
        if ($check = $this->checkLinkProp($param['link']) === 0) {
            return $this->insert($table, $param);
        } else {
            return $this->update($table, $param);
        }
    }

    /**
     * Searching the records for the link
     *
     * @param  string $table
     * @param  string $link
     * @return int
     */
    protected function searchForLink($table, $link)
    {
        $query = $this->pdo->prepare("SELECT id FROM $table WHERE link = '$link'");
        $query->execute();
        return $query->fetchColumn();
    }

    /**
     * Checking for records in the properties table using the link
     *
     * @param  string $link
     * @return int
     */
    protected function checkLinkProp($link)
    {
        $query = $this->pdo->prepare("SELECT EXISTS (SELECT link FROM properties WHERE link = ?)");
        $query->execute([$link]);
        return $query->fetchColumn();
    }

    /**
     * Removing Availability with records update
     *
     * @param  int $id
     * @param  string $table
     */
    public function deleteAvailability($id, $table = 'availability')
    {
        $ids = $this->searchForId($table, $id);

        foreach ($ids as $id) {
            $query = $this->pdo->prepare("DELETE FROM $table WHERE id=?");
            $query->execute([$id->id]);
        }
    }

    /**
     * Getting all records by the difference in date and by is_deleted = 0
     *
     * @param  string $table
     * @return array
     */
    public function getAllRecordsDate($table)
    {
        try {
            $dateNow = date('Y-m-d');
            $query = $this->pdo->prepare(
                "SELECT * FROM $table WHERE (TIMESTAMPDIFF(day, $table.update_at, '$dateNow') >= "
                .env('DIFF_OF_DAYS', 0).
                ") AND is_deleted = 0"
            );
            $query->execute();
            return $query->fetchAll();
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * Searching for the records by ID
     *
     * @param  string $table
     * @param  int $id
     * @return array
     */
    protected function searchForId($table, $id)
    {
        try {
            $query = $this->pdo->prepare("SELECT id FROM $table WHERE property_id = $id");
            $query->execute();
            return $query->fetchAll();
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
    }
}
