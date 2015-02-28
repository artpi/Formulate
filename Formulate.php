<?php
/**
 * Helpers to calculate macros, formulas ant other data inside html.
 *
 * @package    Formulate
 * @author     Artpi <artpi@post.pl>
 * @link       https://github.com/artpi/Formulate
 * @see        https://github.com/artpi/Formulate/example.php
 */

//Load used libraries
$path = realpath(dirname(__FILE__));
require_once $path.'/Spreadsheet.php';
require_once $path.'/evalmath.class.php';

//Initialize them
use Formulate\Spreadsheet\Sheet;
use Formulate\Spreadsheet\FormulaCell;

class Formulate {    
    protected $src;
    protected $result;
    protected $changes = 0;
    protected $math;

    protected function parseSpreadSheet($oldHtml) {

        $sheet = new Sheet($oldHtml, $this->math);
        $newHtml = $sheet->parse();
        
        if ($oldHtml != $newHtml) {
            $this->result = str_replace($oldHtml, $newHtml, $this->result);
            $this->changes++;
            return 1;
        }
        return 0;
    }

    public function parse($html) {
        $this->src = $html;
        $this->result = $html;
        $this->math = new EvalMath;

        //Are there any spreadsheets in the html?
        preg_match_all('#<table[^>]*?>.*?'.FormulaCell::$regex.'.*?</table>#is', $this->src, $results);
        for ($i=0; $i < count($results[0]); $i++) { 
            $this->parseSpreadSheet($results[0][$i]);
        }
        return $this->changes;
    }   
    
    public function getResult() {
        return $this->result;
    }

    public function isChanged() {
        return (($this->changes > 0) && ($this->src != $this->result));
    } 


}
