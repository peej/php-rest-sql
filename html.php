<?php
/* $id$ */

/**
 * PHP REST SQL HTML renderer class
 * This class renders the REST response data as HTML.
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
        header('Content-Type: text/html');
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">';
        echo '<html>';
        echo '<head>';
        echo '<title>PHP REST SQL : Database "'.htmlspecialchars($this->PHPRestSQL->config['database']['database']).'"</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Tables in database "'.htmlspecialchars($this->PHPRestSQL->config['database']['database']).'"</h1>';
        if (isset($this->PHPRestSQL->output['database'])) {
            echo '<ul>';
            foreach ($this->PHPRestSQL->output['database'] as $table) {
                echo '<li><a href="'.htmlspecialchars($table['xlink']).'">'.htmlspecialchars($table['value']).'</a></li>';
            }
            echo '</ul>';
        }
        echo '</body>';
        echo '</html>';
    }
    
    /**
     * Output the rows within a table.
     */
    function table() {
        header('Content-Type: text/html');
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">';
        echo '<html>';
        echo '<head>';
        echo '<title>PHP REST SQL : Table "'.htmlspecialchars($this->PHPRestSQL->table).'"</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Records in table "'.htmlspecialchars($this->PHPRestSQL->table).'"</h1>';
        if (isset($this->PHPRestSQL->output['table'])) {
            echo '<ul>';
            foreach ($this->PHPRestSQL->output['table'] as $row) {
                echo '<li><a href="'.htmlspecialchars($row['xlink']).'">'.htmlspecialchars($row['value']).'</a></li>';
            }
            echo '</ul>';
        }
        echo '</body>';
        echo '</html>';
    }
    
    /**
     * Output the entry in a table row.
     */
    function row() {
        header('Content-Type: text/html');
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">';
        echo '<html>';
        echo '<head>';
        echo '<title>PHP REST SQL : Record #'.htmlspecialchars(join('/', $this->PHPRestSQL->uid)).'</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Record #'.htmlspecialchars(join('/', $this->PHPRestSQL->uid)).'</h1>';
        if (isset($this->PHPRestSQL->output['row'])) {
            echo '<table>';
            foreach ($this->PHPRestSQL->output['row'] as $field) {
                echo '<tr><th>'.htmlspecialchars($field['field']).'</th><td>';
                if (isset($field['xlink'])) {
                    echo '<a href="'.htmlspecialchars($field['xlink']).'">'.htmlspecialchars($field['value']).'</a>';
                } else {
                    echo htmlspecialchars($field['value']);
                }
                echo '</td></tr>';
            }
            echo '</table>';
        }
        echo '</body>';
        echo '</html>';
    }

}

?>
