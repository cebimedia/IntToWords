<?php
/*
 * Convert Integers into Words - NewsMagazines Test
 */

/**
 * Description of Cebi
 *
 * @author tien tien.nguyen@cebimedia.net
 * @todo filter out invalid characters
 * @todo fix this: Negative numbers have no plurals, e.g negative two hundred
 * @todo fix bug with plurals thousandS, millions. write extra code to skip when $fl === 1, it causes output to echo
 * one billions millions thousands
 * @todo write test cases
 * @todo GitHub this
 * @todo reorganise code, needs better dependencies injections
 * @version 1.10
 */

class IntToWords {

        var $space = ' ';
        var $output;
        var $and = 'and';

	var $words = array(
            'digits' => array(
                'zero','one','two','three','four','five',
                'six','seven','eight','nine'
                ),
            'tenth' => array(
                0 => 'ten',
                1 => 'eleven',
                2 => 'twelve',
            ),
            'ty' => array(
                2 => 'twen',
                3 => 'thir',
                4 => 'for',
                5 => 'fif',
                8 => 'eigh'),
            'ceb' => array(//1234567890
                3 => array('thousand', 'thousands'),
                6 => array('million', 'millions'),
                9 => array('billion', 'billions')
            )
	);

	public function __construct()
	{
            //'globalise' input
            $this->output = '';
	}

        public function inputLength($input)
        {
            return strlen(trim($input));
        }

        public function subInt($input,$position,$no = 1)
        {
            $c = array('di'=> -1,'te' => -2,'hu' => -3);
            return substr($input,$c[$position],$no);
        }

        /**
         * Works with digit 0-3
         * @param <int> $input number
         * @return <string>  Words of number input
         */

        public function htd($input)
        {
            $output = '';
            $len = $this->inputLength($input);

            switch($len)
            {
                case 0: //Nothing, but why would you do this?
                    echo 'Empty';
                    break;
                case 1: // 0-9 Digit
                    $output = $this->words['digits'][$this->subInt($input,'di')];
                    break;
                case 2: //19-99
                    $output = $this->tenth($input);
                    break;
                case 3:
                    $output = $this->hundredthbak($input);
                    break;
                default:
                    echo ':) there is an error dude';
                    break;
            }
            return $output;
        }
        /**
         * Works with digit 0-2
         * @param <int> $input number
         * @return <string>  Words of number input
         */
        public function tenth($input)
        {
            $output = '';
            switch ($this->subInt($input,'te')) {
                case 1:
                    switch($this->subInt($input,'di'))
                    {
                        case 0: case 1: case 2:
                            $output = $this->words['tenth'][$this->subInt($input,'di')];
                            break;
                        case 3: case 5: case 8:
                            $output = $this->words['ty'][$this->subInt($input,'di')].'teen';//'thirteen';
                            break;
                        // Fourteen not Forteen
                        case 4: case 6: case 7: case 9:
                            //the rest
                            $output = $this->words['digits'][$this->subInt($input,'di')].'teen';
                            break;
                    }
                    break;
                //20-99
                case 2: case 3: case 4: case 5: case 8:
                    //adds "ty"
                    if($this->subInt($input,'di') == 0)
                    {
                        $output = $this->words['ty'][$this->subInt($input,'te')].'ty';
                    } else {
                        $output = $this->words['ty'][$this->subInt($input,'te')].'ty';
                        $output .= $this->space . $this->words['digits'][$this->subInt($input,'di')];
                    }
                    break;
                case 6: case 7:  case 9:
                    if($this->subInt($input,'di') == 0)
                    {
                        $output = $this->words['digits'][$this->subInt($input,'te')].'ty';
                    } else {
                        $output = $this->words['digits'][$this->subInt($input,'te')].'ty';
                        $output .= $this->space . $this->words['digits'][$this->subInt($input,'di')];
                    }
                    break;
                default:
                    echo ':)';
                    break;
            }
            return $output;
        }
        /**
         *
         * @param <int> $len length of input
         * @param <int> $x position
         * @return string position location
         * @todo fix up the Thousands bug
         */
        private function _pos($len,$x = 0)
        {
            if($this->words['ceb'][$len-$x] !== NULL)
            {
                $pos = ' ' . $this->words['ceb'][($len-$x)] . ' ';
            }
            else
            {
                $pos = '';
            }
            return $pos;
        }
        /**
         * Works with digit from 100-999
         * @param <int> $input number
         * @return <string>  Words of number input
         */
        /**
         *
         * @param <type> $input
         * @return string
         * 001
         * 010
         *
         */
        public function hundredthbak($input)
        {
            $d = $this->subInt($input, 'di');
            $t = $this->subInt($input, 'te');
            $h = $this->subInt($input, 'hu');
            $o = '';
            if($h == 1)
            {
                $o = 'hundred';
            } else {
                $o = 'hundreds';
            }

            if($h == 0)
            {
                if($t == 0)
                {
                    if($d == 0)
                    {
                        $output = '';
                    }
                    else {
                        $output = ' and '.$this->words['digits'][$d];
                    }
                }
                else
                {
                    $output = ' and '.$this->tenth($input);
                }
            } else {
                if($t == 0)
                {
                    if($d == 0 )
                    {
                        $output = $this->words['digits'][$h] . ' ' .$o;
                    } else {
                        $output = $this->words['digits'][$h] . ' ' . $o . ' and ' . $this->words['digits'][$d];
                    }
                }
                else
                {
                    $output = $this->words['digits'][$h] . " $o and " . $this->tenth($input);
                }
            }
            return $output;
        }
        /**
         * Works with numbers that are higher than 4 in lenght
         * @param <int> $input number
         * @return <string>  Words of number input
         */
        public function ttt($input)
        {
            $len = $this->inputLength($input);
            $output = '';
            $fl =   $len % 3;

            //no remainder
            if($fl == 0)
            {
                for($x = 0; $x < $len; $x = $x + 3)
                {
                    $output .= $this->htd(substr($input, $x , 3)). $this->_pos($len, $x);
                }
            } else {
                //Take out the remainder before loop stars
                $output .= $this->htd(substr($input, 0, $fl)) . $this->_pos($len);
                for($x = $fl; $x <= $len-$fl; $x = $x + 3)
                {
                    $output .= $this->htd(substr($input, $x , 3)) . $this->_pos($len, $x);
                }
            }
            return $output;
        }
        public function output($input)
        {
            if(substr($input, 0, 1) == '-')
            {
                //Add word 'Negative' to start
                $this->output = 'Negative' . $this->space;
                //Remove '-' sign from input
                $input = substr($input,1);
            }
            $len = $this->inputLength($input);
            switch($len)
            {
                case 0: case 1: case 2: case 3:
                    $this->output .= $this->htd($input);
                    break;
                case $len > 3:
                    $this->output .= $this->ttt($input);
                    break;
            }
            return $this->output;
        }
}

$a = new IntToWords();
echo $a->output(1000).'<br />';