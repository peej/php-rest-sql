<?php
/* $id$ */

/**
 * PHP REST MySQL class
 * MySQL connection class.
 */
class mysql {
    
    /**
     * @var resource resource
     */
    var $db;
    
    /**
     * Connect to the database.
     * @param str server
     * @param str username
     * @param str password
     */
    function connect($server, $username, $password) {
        if ($this->db = @mysql_pconnect($server, $username, $password)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Close the database connection.
     */
    function close() {
        mysql_close($this->db);
    }
    
    /**
     * Use a database
     */
    function select_db($database) {
        if (mysql_select_db($database, $this->db)) {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Get the columns in a table.
     * @param str table
     * @return resource A resultset resource
     */
    function getColumns($table) {
        return mysql_query(sprintf('SHOW COLUMNS FROM %s', $table), $this->db);   
    }
    
    /**
     * Get a row from a table.
     * @param str table
     * @param str where
     * @return resource A resultset resource
     */
    function getRow($table, $where) {
        return mysql_query(sprintf('SELECT * FROM %s WHERE %s', $table, $where));   
    }
    
    /**
     * Get the rows in a table.
     * @param str primary The names of the primary columns to return
     * @param str table
     * @param str limit
     * @return resource A resultset resource
     */
    function getTable($primary, $table, $limit) {
        if (preg_match('/[0-9]+(,[0-9]+)?/', $limit)) {
            return mysql_query(sprintf('SELECT %s FROM %s LIMIT %s', $primary, $table, $limit));
        } else {
            return mysql_query(sprintf('SELECT %s FROM %s', $primary, $table));
        }
    }

    /**
     * Get the tables in a database.
     * @return resource A resultset resource
     */
    function getDatabase() {
        return mysql_query('SHOW TABLES');   
    }
    
    /**
     * Update a row.
     * @param str table
     * @param str values
     * @param str where
     * @return bool
     */
    function updateRow($table, $values, $where) {
        return mysql_query(sprintf('UPDATE %s SET %s WHERE %s', $table, $values, $where));        
    }
    
    /**
     * Insert a new row.
     * @param str table
     * @param str names
     * @param str values
     * @return bool
     */
    function insertRow($table, $names, $values) {
        return mysql_query(sprintf('INSERT INTO %s (`%s`) VALUES ("%s")', $table, $names, $values));
    }
    
    /**
     * Get the columns in a table.
     * @param str table
     * @return resource A resultset resource
     */
    function deleteRow($table, $where) {
        return mysql_query(sprintf('DELETE FROM %s WHERE %s', $table, $where));   
    }
    
    /**
     * Escape a string to be part of the database query.
     * @param str string The string to escape
     * @return str The escaped string
     */
    function escape($string) {
        return mysql_escape_string($string);
    }
    
    /**
     * Fetch a row from a query resultset.
     * @param resource resource A resultset resource
     * @return str[] An array of the fields and values from the next row in the resultset
     */
    function row($resource) {
        return mysql_fetch_assoc($resource);   
    }

    /**
     * The number of rows in a resultset.
     * @param resource resource A resultset resource
     * @return int The number of rows
     */
    function numRows($resource) {
        return mysql_num_rows($resource);
    }

    /**
     * The number of rows affected by a query.
     * @return int The number of rows
     */
    function numAffected() {
        return mysql_affected_rows($this->db);
    }
    
    /**
     * Get the ID of the last inserted record. 
     * @return int The last insert ID
     */
    function lastInsertId() {
        return mysql_insert_id();   
    }
    
}
?>
