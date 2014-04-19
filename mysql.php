<?php
/*
PHP REST SQL: A HTTP REST interface to relational databases
written in PHP

mysql.php :: MySQL database adapter
Copyright (C) 2004 Paul James <paul@peej.co.uk>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* $id$ */

/**
 * PHP REST MySQL class
 * MySQL connection class.
 */
class mysql {
    
    /**
     * @var resource Database resource
     */
    var $db;
    
    /**
     * Connect to the database.
     * @param str[] config
     */
    function connect($config) {
        if ($this->db = @mysql_pconnect(
            $config['server'],
            $config['username'],
            $config['password']
        )) {
            if ($this->select_db($config['database'])) {
                return TRUE;
            }
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
     * @param str[] contains requested columns
     * @return resource A resultset resource
     */
    function getRow($table, $where, $fields = NULL) {
        $inject = '';
        if($fields == NULL) $inject = "*";
        else {
            $fieldnames = explode(',', $fields);
            foreach($fieldnames as $field) {
                $inject .= $field . ',';            
            }
            // remove redundant comma
            $inject = substr($inject, 0, strlen($inject)-1);
        }
        return mysql_query(sprintf('SELECT %s FROM %s WHERE %s', $inject, $table, $where));
    }
    
    /**
     * Get the rows in a table.
     * @param str primary The names of the primary columns to return
     * @param str table
     * @param int from lowerbound for the LIMIT
     * @param int to denoting the interval starting at lowerbound
     * @param str[] sort contains columns for sorting purposes 
     * @param str[] filter contains search criteria for the rows 
     * @return resource A resultset resource
     */
    function getTable($primary, $table, $from = NULL, $to = NULL, $orderby = NULL, $filters = NULL) {
        
        // prepare LIMIT clause
        $limit_clause = '';
        if(($from != NULL) && ($to != NULL)) {
            $limit_clause .= ' LIMIT ' . $from . ', ' . $to . ' ';
        }
        
        // pepare ORDER BY clause
        $orderbys = explode(',', $orderby);
        if($orderby != NULL) {
            $orderby_clause = 'ORDER BY ';
            foreach($orderbys as $order) {
                if($order[0] == '-') { 
                    $order = ltrim($order, '-');
                    $orderby_clause .= ' ' . $order . ' DESC,';
                } else {
                    $orderby_clause .= ' ' . $order . ' ASC,';
                }
            }
            $orderby_clause = rtrim($orderby_clause, ",");
        } else {
            $orderby_clause = '';
        }
        
        // prepare WHERE clause
        $where_clause = '';
        if(count($filters) > 0) {
            $where_clause = 'WHERE ';
            foreach($filters as $key => $value) {
                $operator = $value[0];
                $value = mysql_real_escape_string(substr($value, 1));
                $key = mysql_real_escape_string($key);
                switch($operator) {
                    case '~':
                        $where_clause .= ' ' . '`' . $key . '`' . ' LIKE ' . '\'%' . $value . '%\'' . ' AND';
                        break;
                    case '=':
                        $where_clause .= ' ' . '`' . $key . '`' . '=' . $value . ' AND';
                        break;
                    case '<':
                        $where_clause .= ' ' . '`' . $key . '`' . '<' . $value . 'AND';
                        break;
                    case '>':
                        $where_clause .= ' ' . '`' . $key . '`' . '>' . $value . ' AND';
                        break;
                }
            }
            $where_clause = rtrim($where_clause, "AND");
        }

        $query = sprintf('SELECT %s FROM %s %s %s %s', $primary, $table, $where_clause, $orderby_clause, $limit_clause);
        return mysql_query($query);
    }

    /**
     * Get the tables in a database.
     * @return resource A resultset resource
     */
    function getDatabase() {
        return mysql_query('SHOW TABLES');
    }

    /**
     * Get the primary keys for the request table.
     * @return str[] The primary key field names
     */
    function getPrimaryKeys($table) {
        $resource = $this->getColumns($table);
        $primary = NULL;
        if ($resource) {
            while ($row = $this->row($resource)) {
                if ($row['Key'] == 'PRI') {
                    $primary[] = $row['Field'];
                }
            }
        }
        return $primary;
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
