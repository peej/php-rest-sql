<?php

/**
 * PHP REST SQL JSON renderer class
 * This class renders the REST response data as JSON.

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
class PHPRestSQLRenderer {
   
    /**
     * @var PHPRestSQL PHPRestSQL
     */
    var $PHPRestSQL;
   
    /**
     * Constructor.
     * @param PHPRestSQL PHPRestSQL
     */
    function render($PHPRestSQL) {
        $this->PHPRestSQL = $PHPRestSQL;
        switch($PHPRestSQL->display) {
            case 'database':
                $this->database();
                break;
            case 'table':
                $this->table();
                break;
            case 'row':
                $this->row();
                break;
        }
    }
    
    /**
     * Output the top level table listing.
     */
    function database() {
        header('Content-Type: application/json');
        if (isset($this->PHPRestSQL->output['database'])) {
					echo json_encode($this->PHPRestSQL->output['database']);
        } 
    }
    
    /**
     * Output the rows within a table.
     */
    function table() {
        header('Content-Type: application/json');
        if (isset($this->PHPRestSQL->output['table'])) {
            echo 	json_encode($this->PHPRestSQL->output['table']);
        }
    }
    
    /**
     * Output the entry in a table row.
     */
    function row() {
        header('Content-Type: application/json');
        if (isset($this->PHPRestSQL->output['row'])) {
            echo 	json_encode($this->PHPRestSQL->output['row']);				
        }
    }

}
