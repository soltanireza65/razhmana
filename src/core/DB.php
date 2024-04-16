<?php

namespace MJ\Database;


use MJ\Keys\KEYS;
use PDO;
use PDOException;
use stdClass;
use function MJ\Keys\sendResponse;


require_once $_SERVER['DOCUMENT_ROOT'] . '/core/KEYS.php';


/**
 * Class Database
 * @package MJ\Database
 */
class DB
{
    /**
     * 
     * @return PDO|null
     * @version 1.0.0
     * @author Tjavan
     */
    private static function connectDB()
    {
        try {
            $dbh = "mysql:host=%HOST%;dbname=%DBNAME%";
            $dbh = str_replace('%HOST%', KEYS::$host, $dbh);
            $dbh = str_replace('%DBNAME%', KEYS::$dbName, $dbh);
            $connection = new PDO($dbh, KEYS::$dbUserName, KEYS::$dbPassword);
            $connection->exec("SET character_set_connection = 'utf8mb4'");
            $connection->exec("SET NAMES 'utf8mb4'");
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $err) {
            return null;
        }
    }


    /**
     * 
     * @param string $table
     * @param array $columns
     * @param array $conditions
     * @param array $conditionsOperators
     * @param array $conditionsArgs
     * @param string | null $groupBy
     * @param string | null $having
     * @param array $orderBy
     * @param string | int $limit
     * @return stdClass
     * @author Tjavan, Morteza
     * @version 1.0.0
     */
    public static function select($table, $columns = [], $conditions = [], $conditionsOperators = [], $conditionsArgs = [], $groupBy = null, $having = null, $orderBy = [], $limit = 1000)
    {
        $queryColumns = self::columnsToQuery($columns);
        $queryCondition = self::conditionToQuery($conditions, $conditionsOperators, $conditionsArgs);
        $queryGroupBy = self::groupByToQuery($groupBy);
        $queryHaving = self::havingToQuery($having);
        $queryOrderBy = self::orderByToQuery($orderBy);
        $queryLimit = self::limitToQuery($limit);
        $query = "select {$queryColumns} from {$table}{$queryCondition}{$queryGroupBy}{$queryHaving}{$queryOrderBy}{$queryLimit};";

        return self::rawQuery($query);
    }


    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function rawQuery($query, $bindings = [])
    {
        $connection = self::connectDB();
        $response = sendResponse(-1, "Couldn't connect to database.");

        if (!empty($connection)) {
            try {
                $response = sendResponse(204, 'No records found.');

                $statement = $connection->prepare($query);
                $statement->execute($bindings);
                $result = $statement->fetchAll(PDO::FETCH_OBJ);
                if (count($result) > 0) {
                    $response = sendResponse(200, count($result) . " Records found.", $result);
                }
            } catch (PDOException $err) {
                $response = sendResponse(-2, "Your SQL statement have errors.");
            }
        }

        return $response;
    }



    public static function transactionQuery($query, $bindings = [])
    {
        $connection = self::connectDB();
        $response = sendResponse(-1, "Couldn't connect to database.");

        if (!empty($connection)) {
            try {
                $response = sendResponse(202, "Transaction failed");

                $connection->beginTransaction();
                $statement = $connection->prepare($query);
                foreach ($bindings as $key => $value) {
                    $statement->bindValue(":".$key, $value);
                }
                if ($connection->commit() &&  $statement->execute()){
                    $response = sendResponse(200, "Transaction successfully");
                }
//                $statement = $connection->prepare($query);
//                $statement->execute($bindings);
//                $result = $statement->fetchAll(PDO::FETCH_OBJ);
//                if (count($result) > 0) {
//                    $response = sendResponse(200, count($result) . " Records found.", $result);
//                }
            } catch (PDOException $err) {
                $connection->rollBack();
                $response = sendResponse(-2, "Your SQL statement have errors.");
            }
        }

        return $response;
    }


    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function insert($query, $bindings = [])
    {
        $connection = self::connectDB();
        $response = sendResponse(-1, "Couldn't connect to database.");

        if (!empty($connection)) {
            try {
                $response = sendResponse(202, 'No row added.');

                $statement = $connection->prepare($query);
                $statement->execute($bindings);
                if ($connection->lastInsertId()) {
                    $response = sendResponse(200, 'New row created successfully.', $connection->lastInsertId());
                }
            } catch (PDOException $err) {
                $response = sendResponse(-2, "Your SQL statement have errors.");
            }
        }

        return $response;
    }


    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function update($query, $bindings = [])
    {
        $connection = self::connectDB();
        $response = sendResponse(-1, "Couldn't connect to database.");

        if (!empty($connection)) {
            try {
                $response = sendResponse(208, 'Changes have already applied.');

                $statement = $connection->prepare($query);
                $statement->execute($bindings);
                if ($statement->rowCount() > 0) {
                    $response = sendResponse(200, 'Changes applied successfully.');
                }
            } catch (PDOException $err) {
                $response = sendResponse(-2, "Your SQL statement have errors.");
            }
        }

        return $response;
    }


    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function delete($query, $bindings = [])
    {
        $connection = self::connectDB();
        $response = sendResponse(-1, "Couldn't connect to database.");

        if (!empty($connection)) {
            try {
                $response = sendResponse(203, 'No row to delete found.');

                $statement = $connection->prepare($query);
                $statement->execute($bindings);
                if ($statement->rowCount() > 0) {
                    $response = sendResponse(200, 'Row deleted successfully.');
                }
            } catch (PDOException $err) {
                $response = sendResponse(-2, "Your SQL statement have errors.");
            }
        }

        return $response;
    }


    /**
     * 
     * @param array $columns
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    private static function columnsToQuery($columns = [])
    {
        $queryColumns = "";

        if (!empty($columns)) {
            $queryColumns .= implode(', ', $columns);
        } else {
            $queryColumns .= "*";
        }

        return $queryColumns;
    }


    /**
     * 
     * @param array $conditions
     * @param array $conditionsOperators
     * @param array $conditionsArgs
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    private static function conditionToQuery($conditions, $conditionsOperators, $conditionsArgs)
    {
        $queryConditions = "";

        if (!empty($conditions) && !empty($conditionsArgs)) {
            $queryConditions = " where ";
            for ($i = 0; $i < count($conditions); $i++) {
                $queryConditions .= "{$conditions[$i]} = '{$conditionsArgs[$i]}' ";
                if (isset($conditionsOperators[$i])) {
                    $queryConditions .= "{$conditionsOperators[$i]} ";
                }
            }
        }

        return $queryConditions;
    }


    /**
     * 
     * @param string $groupBy
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    private static function groupByToQuery($groupBy)
    {
        $queryGroupBy = "";

        if (!empty($groupBy)) {
            $queryGroupBy = " group by {$groupBy}";
        }

        return $queryGroupBy;
    }


    /**
     * 
     * @param string $having
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    private static function havingToQuery($having)
    {
        $queryHaving = "";

        if (!empty($having)) {
            $queryHaving = " having {$having}";
        }

        return $queryHaving;
    }


    /**
     * 
     * @param array $orderBy
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    private static function orderByToQuery($orderBy)
    {
        $queryOrderBy = "";

        if (!empty($orderBy)) {
            $queryOrderBy = " order by " . implode(', ', $orderBy);
        }

        return $queryOrderBy;
    }


    /**
     * 
     * @param string | int $limit
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    private static function limitToQuery($limit)
    {
        $queryLimit = "";

        if (!empty($limit)) {
            $queryLimit = " limit {$limit}";
        }

        return $queryLimit;
    }



}