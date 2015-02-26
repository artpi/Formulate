<?php

/**
 * Spreadsheet helpers to calculate formulas used in HTML table
 *
 * @package    Formulate
 * @author     Artpi <artpi@post.pl>
 * @link       https://github.com/artpi/Formulate
 * @see        https://github.com/artpi/Formulate/example.php
 */

namespace Spreadsheet;

    class Cell {
        public $value;
        public $calculated = 2; // 0 for not yet, 1 for in calculation, 2 for calculated;
        public $table;
        public $index;
        public $x;
        public $y;

        function getValue() {
            return "NAN";
        }

        function get() {
            return $this->value;
        }

        function setup($table, $index, $x, $y) {
            $this->table = $table;
            $this->index = $index;
            $this->x = $x;
            $this->y = $y;
        }

        function result() {
            return false;    
        }

        static function getInstance($value) {
            $ret = new self();
            $ret->value = $value;
            return $ret;
        }


    }


    class NumCell extends Cell{

        function getValue() {
            return $this->get();
        }

        static function getInstance($value) {
            if(is_numeric($value)) {
                $ret = new self();
                $ret->value = $value;
                return $ret;
            } else {
                return false;
            }
        }

    }


    class FormulaCell extends NumCell{
        public $formula;
        private $error = 0;
        private $origin;

        function getValue() {
            $formula = preg_replace_callback("#[A-Z]+[0-9]+#is", function ($adr) {
                $index = $adr[0];
                $val = $this->table->data[$index]->getValue();
                if($val == "NAN") {
                    $this->error = "NAN";
                }
                return $val;
            }, $this->formula);

            if($this->error == 0) {
                $this->value = $this->table->math->evaluate($formula);
            } else {
                $this->value = "NAN";
            }
            
            return $this->value;
        }

        function get() {
            $this->getValue();
            if($this->value == "NAN") {
                $val = "#NaN";
            } else {
                $val = $this->value;
            }

            return "{".$this->formula."=".$val."}";
        }

        function result() {
            $result = $this->get();
            return array($this->origin, $result);
        }

        static function getInstance($value) {
            if(preg_match('#\{([A-Z0-9/+\-\*\(\)\.\, ]+)=([ 0-9.]*?(\#NaN)?)\}#is', $value, $result)) {
                $ret = new self();
                $ret->value = $result[2];
                $ret->formula = str_replace(array(' ',','),array('','.'),$result[1]);
                $ret->origin = $result[0];
                return $ret;
            } else {
                return false;
            }
        }

    }





    class Sheet {
        public $data = array();
        public $origin = '';
        public $math;
        
        function makeCell($row, $column, $val) {
            $row++;
            $col = 'A';
            for($i=0;$i<$column;$i++) {
                ++$col;
            }

            $index = $col.$row;
            $val = trim(strip_tags($val));

            if($cell = FormulaCell::getInstance($val)) {
            } else if($cell = NumCell::getInstance($val)) {
            } else {
                $cell = Cell::getInstance($val);
            }

            $cell->setup($this, $index, $column, $row);
            $this->data[$index] = $cell;
        }

        function parse() {
            foreach ($this->data as $key => $cell) {
                if($replace = $cell->result()) {
                    $this->origin = str_replace($replace[0], $replace[1], $this->origin);
                }
            }

            return $this->origin;
        }


        function __construct($table, $math = null) {
            if(!$math) {
                $math = new EvalMath;
            }
            $this->math = $math;
            $this->origin = $table;
            preg_match_all('#<tr[^<]*?>.*?<\/tr>#is', $table, $rows);

            for ($i=0; $i < count($rows[0]); $i++) { 
                if(preg_match_all('#<td[^<]*?>(.*?)<\/td>#is', $rows[0][$i], $columns)) {
                    for ($j=0; $j < count($columns[0]); $j++) { 
                        $this->makeCell($i, $j, $columns[1][$j]);
                    }
                }
            }
        }

    }



?>

