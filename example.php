<?php

require_once 'Formulate.php';

//Basically, the whole code is encapsulated in this:
function form($str) {
    $f = new Formulate();
    $f->parse($str);
    if($f->isChanged()) {
        return $f->getResult();
    } else {
        return "No changes detected";
    }
}



//Let's roll with some tests

function test($src, $dst) {
    $result = form($src);

    if(trim($result) == trim($dst)) {
        echo "Test OK \n";
    } else {
        echo "---------TEST FAILED----------\n";
        echo $src;
        echo "\n-------RESULT:--------------\n";
        echo $result;
        echo "\n";
    }
}



test('
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
','<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>{C2-C1=3}</td><td></td><td>{A1+B1/(C2-A2)-8-(1*6)=-9}</td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>{A1+A2=9}</td><td>8</td></tr>
</table>');




test('
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=}</td></tr>
<tr><td>3</td><td>4</td><td>{A2+B2=}</td></tr>
<tr><td>6</td><td>7</td><td>{A3+B3=}</td></tr>
<tr><td>6</td><td>7</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=33}</td></tr>
<tr><td>3</td><td>4</td><td>{A2+B2=7}</td></tr>
<tr><td>6</td><td>7</td><td>{A3+B3=13}</td></tr>
<tr><td>6</td><td>7</td><td>{A4+B4=13}</td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=}</td></tr>
<tr><td>3</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=34.7833444}</td></tr>
<tr><td>3</td><td>4.5</td><td>{A2+B2=7.5}</td></tr>
<tr><td>6</td><td>7.21</td><td>{A3+B3=13.21}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
</table>
');


test('
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=35.6498944}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4/B78=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4/B78=#RANGE}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{A5=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A5=17}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{A5=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A5=#NAN}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{A2 + potato()=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A2+potato()=#NAME}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');


test('
<table>
<tr><td>SUMA</td><td></td><td>{C1=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C1=#LOOP}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{C4+5=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C4+5=#LOOP}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=#LOOP}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+3=#LOOP}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+4=#LOOP}</td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+C1=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+C1=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=9.86655}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=11.86655}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+C1=21.7331}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+C1=31.59965}</td></tr>
</table>
');

test('
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=9.86655}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=19.7331}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=2}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=-7.86655}</td></tr>
</table>
');
