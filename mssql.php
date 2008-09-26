<?php
/*
PHP REST SQL: A HTTP REST interface to relational databases
written in PHP

mssql.php :: Mssql database adapter
Copyright (C) 2008 Lee Smith <lee@aionex.com>

based on MySQL adapter mysql.php by Paul James
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
 * PHP REST Mssql class
 * Mssql connection class.
 */
class mssql {
    
	/**
	 * @var int
	 */
	var $lastInsertPKeys;
	
	/**
	 * @var resource
	 */
    var $lastQueryResultResource;
    
    /**
     * @var resource Database resource
     */
    var $db;
    
    /**
     * Connect to the database.
     * @param str[] config
     */
    function connect($config) {
		
		$connString = sprintf(
			'host=%s dbname=%s user=%s password=%s',
			$config['server'],
			$config['database'],
			$config['username'],
			$config['password']
		);
		
		if ($this->db = mssql_pconnect($config['server'], $config['username'], $config['password'])) {
		    mssql_select_db($config['database']);
		    return TRUE;
	    }
		return FALSE;
    }

    /**
     * Close the database connection.
     */
    function close() {
        mssql_close($this->db);
    }
    
    /**
     * Get the columns in a table.
     * @param str table
     * @return resource A resultset resource
     */
    function getColumns($table) {
    	$qs = sprintf('SELECT * FROM information_schema.columns WHERE table_name =\'%s\'', $table);
		return mssql_query($qs, $this->db);
    }
    
    /**
     * Get a row from a table.
     * @param str table
     * @param str where
     * @return resource A resultset resource
     */
    function getRow($table, $where) {
        $result = mssql_query(sprintf('SELECT * FROM %s WHERE %s', $table, $where));   
    	if ($result) {
	        $this->lastQueryResultResource = $result;
	    }
        return $result;
    }
    
    /**
     * Get the rows in a table.
     * @param str primary The names of the primary columns to return
     * @param str table
     * @return resource A resultset resource
     */
    function getTable($primary, $table) {
        $result = mssql_query(sprintf('SELECT %s FROM %s', $primary, $table));  
        if ($result) {
            $this->lastQueryResultResource = $result;
        }
        return $result;        
    }

    /**
     * Get the tables in a database.
     * @return resource A resultset resource
     */
    function getDatabase() {
        return mssql_query('SELECT table_name FROM information_schema.tables');   
    }
	
    /**
     * Get the primary keys for the request table.
     * @return str[] The primary key field names
     */
    function getPrimaryKeys($table) {
      $primary = NULL;
      $query = sprintf("SELECT [name]
  FROM syscolumns 
	WHERE [id] IN (SELECT [id] 
                  FROM sysobjects 
		       WHERE [name] = '%s')
	AND colid IN (SELECT SIK.colid 
                   FROM sysindexkeys SIK 
                   JOIN sysobjects SO ON SIK.[id] = SO.[id]  
                  WHERE SIK.indid = 1
		      AND SO.[name] = '%s')", $table, $table);


      $result = mssql_query($query);
      //      while($row = mssql_fetch_assoc($result))
      $row = mssql_fetch_assoc($result);
	{
	  if($row)
	    $primary[] = $row['name'];
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
        # translate from MySQL syntax :)
        $values = preg_replace('/"/','\'',$values);
        $values = preg_replace('/`/','"',$values); 
        $qs = sprintf('UPDATE %s SET %s WHERE %s', $table, $values, $where);
        $result = mssql_query($qs);       
        if ($result) {
            $this->lastQueryResultResource = $result;
        }
        return $result;
    }
    
    /**
     * Insert a new row.
     * @param str table
     * @param str names
     * @param str values
     * @return bool
     */
    function insertRow($table, $names, $values) {
        # translate from MySQL syntax
		$names = preg_replace('/`/', '"', $names); #backticks r so MySQL-ish! ;)
        $values = preg_replace('/"/', '\'', $values);
        $pkeys = join(', ', $this->getPrimaryKeys($table));
        
        $qs = sprintf(
			'INSERT INTO $table ("%s") VALUES ("%s") RETURNING (%s)',
			$names,
			$values,
			$pkeys
		);
        $result = mssql_query($qs); #or die(pg_last_error());
		
        $lastInsertPKeys = mssql_fetch_row($result);
        $this->lastInsertPKeys = $lastInsertPKeys;
		
        if ($result) {
            $this->lastQueryResultResource = $result;
        }
        return $result;
    }
    
    /**
     * Get the columns in a table.
     * @param str table
     * @return resource A resultset resource
     */
    function deleteRow($table, $where) {
        $result = mssql_query(sprintf('DELETE FROM %s WHERE %s', $table, $where));   
        if ($result) {
            $this->lastQueryResultResource = $result;
        }
        return $result;
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
        return mssql_fetch_assoc($resource);
    }

    /**
     * The number of rows in a resultset.
     * @param resource resource A resultset resource
     * @return int The number of rows
     */
    function numRows($resource) {
        return mssql_num_rows($resource);
    }

    /**
     * The number of rows affected by a query.
     * @return int The number of rows
     */
    function numAffected() {
        return mssql_rows_affected($this->lastQueryResultResource);
    }
    
    /**
     * Get the ID of the last inserted record. 
     * @return int The last insert ID ('a/b' in case of multi-field primary key)
     */
    function lastInsertId() {
        return join('/', $this->lastInsertPKeys);
    }
    
}
?>
