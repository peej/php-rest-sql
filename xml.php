<?php

/**
 * PHP REST SQL XML renderer class
 * This class renders the REST response data as XML.
 */
class PHPRestSQLRenderer {
   
    /**
     * @var PHPRestSQL PHPRestSQL
     */
    var $PHPRestSQL;
   
    /**
     * Constructor. Takes an output array and calls the appropriate handler.
     * @param PHPRestSQL PHPRestSQL
     */
    function render($PHPRestSQL) {
        $this->PHPRestSQL = $PHPRestSQL;
        if (isset($PHPRestSQL->output['database'])) {
            $this->database();
        } elseif (isset($PHPRestSQL->output['table'])) {
            $this->table();
        } elseif (isset($PHPRestSQL->output['row'])) {
            $this->row();
        }
    }
    
    /**
     * Output the top level table listing.
     */
    function database() {
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<database xmlns:xlink="http://www.w3.org/1999/xlink">';
        foreach ($this->PHPRestSQL->output['database'] as $table) {
            echo '<table xlink:href="'.htmlspecialchars($table['xlink']).'">'.htmlspecialchars($table['value']).'</table>';
        }
        echo '</database>'; 
    }
    
    /**
     * Output the rows within a table.
     */
    function table() {
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<table xmlns:xlink="http://www.w3.org/1999/xlink">';
        foreach ($this->PHPRestSQL->output['table'] as $row) {
            echo '<row xlink:href="'.htmlspecialchars($row['xlink']).'">'.htmlspecialchars($row['value']).'</row>';
        }
        echo '</table>'; 
    }
    
    /**
     * Output the entry in a table row.
     */
    function row() {
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<row xmlns:xlink="http://www.w3.org/1999/xlink">';
        foreach ($this->PHPRestSQL->output['row'] as $field) {
            $fieldName = $field['field'];
            echo '<'.$fieldName;
            if (isset($field['xlink'])) {
                echo ' xlink:href="'.htmlspecialchars($field['xlink']).'"';
            }
            echo '>'.htmlspecialchars($field['value']).'</'.$fieldName.'>';
        }
        echo '</row>';
    }

}
