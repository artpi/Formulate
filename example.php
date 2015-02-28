<?php

require_once 'Formulate.php';


$str = '
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>{C2-C1=}</td><td></td><td>{A1+B1/(C2-A2) - 8 - (1 * 6)=}</td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>{A1+A2=}</td><td>8</td></tr>
</table>
';


$f = new Formulate();
$f->parse($str);
if($f->isChanged()) {
    echo $f->getResult();
} else {
    echo "No changes detected";
}



/*
RESULT: (notice the =-9 and =9 ?)

<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>{C2-C1=3}<S/td><td></td><td>{A1+B1/(C2-A2)-8-(1*6)=-9}</td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>{A1+A2=9}</td><td>8</td></tr>
</table>

*/