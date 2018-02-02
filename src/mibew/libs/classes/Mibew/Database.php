<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew;

/**
 * Encapsulates work with database. Implenets singleton pattern to provide only
 * one instance.
 */
class Database
{
    const FETCH_ASSOC = 1;
    const FETCH_NUM = 2;
    const FETCH_BOTH = 4;
    const RETURN_ONE_ROW = 8;
    const RETURN_ALL_ROWS = 16;

    /**
     * An instance of Database class
     * @var Database
     */
    protected static $instance = null;

    /**
     * PDO object
     * @var \PDO
     */
    protected $dbh = null;

    /**
     * Database host
     * @var string
     */
    protected $dbHost = '';

    /**
     * Database port
     * @var int
     */
    protected $dbPort = 0;

    /**
     * Database user's login
     * @var string
     */
    protected $dbLogin = '';

    /**
     * Database user's password
     * @var string
     */
    protected $dbPass = '';

    /**
     * Database name
     * @var string
     */
    protected $dbName = '';

    /**
     * Tables prefix
     * @var string
     */
    protected $tablesPrefix = '';

    /**
     * Determine if connection to the database must be persistent
     * @var boolean
     */
    protected $usePersistentConnection = false;

    /**
     * Array of prepared SQL statements
     * @var array
     */
    protected $preparedStatements = array();

    /**
     * Id of the last query
     * @var string|null
     */
    protected $lastQuery = null;

    /**
     * Controls if exception must be processed into class or thrown
     * @var boolean
     */
    protected $useExceptions = false;

    /**
     * Get instance of Database class.
     *
     * If no instance exists, creates new instance.
     * Use Database::initialize() before trying to get an instance.
     * If database was not initialized correctly triggers an error
     * with E_USER_ERROR level.
     *
     * @return Database
     * @see Database::initialize()
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            trigger_error('Database was not initialized correctly', E_USER_ERROR);
        }

        return self::$instance;
    }

    /**
     * Destroy internal database object
     */
    public static function destroy()
    {
        if (!is_null(self::$instance)) {
            self::$instance->__destruct();
            self::$instance = null;
        }
    }

    /**
     * Initialize database.
     *
     * Set internal database and connectionproperties. Create Database object.
     * Create PDO object and store it in the Database object.
     *
     * @param string $host Database host.
     * @param int $port Database port.
     * @param string $user Database user name.
     * @param string $pass Database for user with $name password.
     * @param boolean $use_pconn Control use persistent connection to the
     *   database or not.
     * @param string $db Database name.
     * @param string $prefix Database tables prefix
     */
    public static function initialize($host, $port, $user, $pass, $use_pconn, $db, $prefix)
    {
        // Check PDO
        if (!extension_loaded('PDO')) {
            throw new \Exception('PDO extension is not loaded');
        }

        if (!extension_loaded('pdo_mysql')) {
            throw new \Exception('pdo_mysql extension is not loaded');
        }

        // Check if initialization
        if (!is_null(self::$instance)) {
            throw new \Exception('Database already initialized');
        }

        // Create database instance
        $instance = new Database();

        // Set database and connection properties
        $instance->dbHost = $host;
        $instance->dbPort = $port;
        $instance->dbLogin = $user;
        $instance->dbPass = $pass;
        $instance->dbName = $db;
        $instance->tablesPrefix = preg_replace('/[^A-Za-z0-9_$]/', '', $prefix);
        $instance->usePersistentConnection = $use_pconn;

        // Create PDO object
        $instance->dbh = new \PDO(
            "mysql:host={$instance->dbHost};port={$instance->dbPort};dbname={$instance->dbName}",
            $instance->dbLogin,
            $instance->dbPass,
            array(\PDO::ATTR_PERSISTENT => $instance->usePersistentConnection)
        );

        // Force charset in all connections
        $instance->dbh->exec("SET NAMES utf8");

        // Store instance
        self::$instance = $instance;
    }

    /**
     * Checks if the database was initialized correctly.
     *
     * @return boolean True if the database was initialized correctly and false
     *   otherwise.
     */
    public static function isInitialized()
    {
        return !is_null(self::$instance);
    }

    /**
     * Set if exceptions must be process into the class or thrown and return
     * previous value.
     *
     * If called without arguments just return previous value without changing
     * anything.
     *
     * There is a typo in the method name. One should use
     * {@link Database::throwExceptions()} method instead.
     *
     * @deprecated since version 2.0.0
     * @param boolean|null $value Value that should be set. This argument is
     * optional and can be skipped (or set to null) to keep the internal value
     * unchanged.
     * @return bool Previous value
     */
    public function throwExeptions($value = null)
    {
        return $this->throwExceptions($value);
    }

    /**
     * Set if exceptions must be process into the class or thrown and return
     * previous value.
     *
     * If called without arguments just return previous value without changing
     * anything.
     *
     * @param boolean|null $value Value that should be set. This argument is
     * optional and can be skipped (or set to null) to keep the internal value
     * unchanged.
     * @return bool Previous value
     */
    public function throwExceptions($value = null)
    {
        $last_value = $this->useExceptions;
        if (!is_null($value)) {
            $this->useExceptions = $value;
        }

        return $last_value;
    }

    /**
     * Database class destructor.
     */
    public function __destruct()
    {
        foreach ($this->preparedStatements as $key => $statement) {
            $this->preparedStatements[$key] = null;
        }
        $this->dbh = null;
        self::$instance = null;
    }

    /**
     * Executes SQL query.
     *
     * In SQL query can be used PDO style placeholders:
     * unnamed placeholders (question marks '?') and named placeholders (like
     * ':name'). If unnamed placeholders are used, $values array must have
     * numeric indexes. If named placeholders are used, $values param must be an
     * associative array with keys corresponding to the placeholders names
     *
     * Table prefix automatically substitute if table name puts in curly braces
     *
     * @param string $query SQL query
     * @param array $values Values, that must be substitute instead of
     *   placeholders in SQL query.
     * @param array $params Array of query parameters. It can contains values
     *   with following keys:
     *   - 'return_rows' control if rows must be returned and how many rows must
     *     be returned. The value can be Database::RETURN_ONE_ROW for only one
     *     row or Database::RETURN_ALL_ROWS for all rows. If this key not
     *     specified, the function will not return any rows.
     *   - 'fetch_type' control indexes in resulting rows. The value can be
     *     Database::FETCH_ASSOC for associative array, Database::FETCH_NUM for
     *     array with numeric indexes and Database::FETCH_BOTH for both indexes.
     *     Default value is Database::FETCH_ASSOC.
     * @return mixed If 'return_rows' key of the $params array is specified,
     *   returns one or several rows (depending on $params['return_rows'] value)
     *   or boolean false on fail.
     *   If 'return_rows' key of the $params array is not specified, returns
     *   boolean true on success or false on fail.
     *
     * @see Database::RETURN_ONE_ROW
     * @see Database::RETURN_ALL_ROWS
     * @see Database::FETCH_ASSOC
     * @see Database::FETCH_NUM
     * @see Database::FETCH_BOTH
     */
    public function query($query, $values = null, $params = array())
    {
        try {
            $query = preg_replace("/\{(\w+)\}/", $this->tablesPrefix . "$1", $query);

            $query_key = md5($query);
            if (!array_key_exists($query_key, $this->preparedStatements)) {
                $this->preparedStatements[$query_key] = $this->dbh->prepare($query);
            }

            $this->lastQuery = $query_key;

            // Execute query
            $this->preparedStatements[$query_key]->execute($values);

            // Check if error occurs
            if ($this->preparedStatements[$query_key]->errorCode() !== '00000') {
                $errorInfo = $this->preparedStatements[$query_key]->errorInfo();
                throw new \Exception(' Query failed: ' . $errorInfo[2]);
            }

            // No need to return rows
            if (!array_key_exists('return_rows', $params)) {
                return true;
            }

            // Some rows must be returned
            // Get indexes type
            if (!array_key_exists('fetch_type', $params)) {
                $params['fetch_type'] = Database::FETCH_ASSOC;
            }
            switch ($params['fetch_type']) {
                case Database::FETCH_NUM:
                    $fetch_type = \PDO::FETCH_NUM;
                    break;
                case Database::FETCH_ASSOC:
                    $fetch_type = \PDO::FETCH_ASSOC;
                    break;
                case Database::FETCH_BOTH:
                    $fetch_type = \PDO::FETCH_BOTH;
                    break;
                default:
                    throw new \Exception("Unknown 'fetch_type' value!");
            }

            // Get results
            $rows = array();
            if ($params['return_rows'] == Database::RETURN_ONE_ROW) {
                $rows = $this->preparedStatements[$query_key]->fetch($fetch_type);
            } elseif ($params['return_rows'] == Database::RETURN_ALL_ROWS) {
                $rows = $this->preparedStatements[$query_key]->fetchAll($fetch_type);
            } else {
                throw new \Exception("Unknown 'return_rows' value!");
            }
            $this->preparedStatements[$query_key]->closeCursor();

            return $rows;
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Returns value of PDOStatement::$errorInfo property for last query.
     *
     * @return string Error info array
     * @see \PDOStatement::$erorrInfo
     */
    public function errorInfo()
    {
        if (is_null($this->lastQuery)) {
            return false;
        }
        try {
            $error_info = $this->preparedStatements[$this->lastQuery]->errorInfo();
        } catch (\Exception $e) {
            $this->handleError($e);
        }

        return $error_info;
    }

    /**
     * Returns the ID of the last inserted row
     *
     * @return int The ID
     */
    public function insertedId()
    {
        try {
            $last_inserted_id = $this->dbh->lastInsertId();
        } catch (\Exception $e) {
            $this->handleError($e);
        }

        return $last_inserted_id;
    }

    /**
     * Get count of affected rows in the last query
     *
     * @return int Affected rows count
     */
    public function affectedRows()
    {
        if (is_null($this->lastQuery)) {
            return false;
        }
        try {
            $affected_rows = $this->preparedStatements[$this->lastQuery]->rowCount();
        } catch (\Exception $e) {
            $this->handleError($e);
        }

        return $affected_rows;
    }

    /**
     * Forbid external object creation
     */
    protected function __construct()
    {
    }

    /**
     * Handles errors
     * @param \Exception $e
     */
    protected function handleError(\Exception $e)
    {
        if ($this->useExceptions) {
            throw $e;
        }
        die($e->getMessage());
    }

    /**
     * Forbid clone objects
     */
    final private function __clone()
    {
    }
}
