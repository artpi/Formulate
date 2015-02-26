<?php

require 'Spreadsheet.php';
require 'evalmath.class.php';

use Spreadsheet\Sheet;

$str = '<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td></td><td></td><td>{A1+B1/(C2-A2) - 8 - (1 * 6)=}</td></tr>
</table>';

$m = new EvalMath;
$t = new Sheet($str, $m);
echo $t->parse();


/*
RESULT: (notice the -9 ?)

<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td></td><td></td><td>{A1+B1/(C2-A2) - 8 - (1 * 6)=-9}</td></tr>
</table>

*/