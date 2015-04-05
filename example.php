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

function test($objective, $src, $dst) {
    $result = form($src);

    if(trim($result) == trim($dst)) {
        echo "OK: `".$objective."`\n";
    } else {
        echo "---------TEST `".$objective."` FAILED----------\n";
        echo $src;
        echo "\n-------RESULT:--------------\n";
        echo $result;
        echo "\n";
    }
}



test("Multiple sheets", '<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>=C2-C1</td><td></td><td>=A1+B1/(C2-A2) - 8 - (1 * 6)</td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>=A1+A2</td><td>8</td></tr>
</table>
','<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>=C2-C1<br/><strong>3</strong></td><td></td><td>=A1+B1/(C2-A2)-8-(1*6)<br/><strong>-9</strong></td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>=A1+A2<br/><strong>9</strong></td><td>8</td></tr>
</table>');




test("Some other test", '
<table>
<tr><td>SUMA</td><td></td><td>=C2+C3+C4</td></tr>
<tr><td>3</td><td>4</td><td>=A2+B2</td></tr>
<tr><td>6</td><td>7</td><td>=A3+B3</td></tr>
<tr><td>6</td><td>7</td><td>=A4+B4</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=C2+C3+C4<br/><strong>33</strong></td></tr>
<tr><td>3</td><td>4</td><td>=A2+B2<br/><strong>7</strong></td></tr>
<tr><td>6</td><td>7</td><td>=A3+B3<br/><strong>13</strong></td></tr>
<tr><td>6</td><td>7</td><td>=A4+B4<br/><strong>13</strong></td></tr>
</table>
');



test("Fractions",'
<table>
<tr><td>SUMA</td><td></td><td>=C2+C3+C4</td></tr>
<tr><td>3</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=C2+C3+C4<br/><strong>34.7833444</strong></td></tr>
<tr><td>3</td><td>4.5</td><td>=A2+B2<br/><strong>7.5</strong></td></tr>
<tr><td>6</td><td>7.21</td><td>=A3+B3<br/><strong>13.21</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
</table>
');


test(", in fractions", '
<table>
<tr><td>SUMA</td><td></td><td>=C2+C3+C4</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=C2+C3+C4<br/><strong>35.6498944</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
</table>
');

test("Out of range", '
<table>
<tr><td>SUMA</td><td></td><td>=C2+C3+C4/B78</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=C2+C3+C4/B78<br/><strong>#RANGE</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
</table>

');

test("Minimal formual", '
<table>
<tr><td>SUMA</td><td></td><td>=A5</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=A5<br/><strong>17</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
');


test("Recalculating error codes", '
<table>
<tr><td>SUMA</td><td></td><td>=A5<br/><strong>#RANGE</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>#NAN</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>#LOOP</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>#LOOP</strong></td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=A5<br/><strong>17</strong></strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></strong></td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
');


test("NAN", '
<table>
<tr><td>SUMA</td><td></td><td>=A5</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=A5<br/><strong>#NAN</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');

test("Error in formula", '
<table>
<tr><td>SUMA</td><td></td><td>=A2 + potato()</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=A2+potato()<br/><strong>#NAME</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');


test("Circular dependency", '
<table>
<tr><td>SUMA</td><td></td><td>=C1</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=C1<br/><strong>#LOOP</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=A2+B2<br/><strong>7.93335</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=A4+B4<br/><strong>14.0733444</strong></td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');

test("Multiple circular depenedencies", '
<table>
<tr><td>SUMA</td><td></td><td>=C4+5</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1+2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2+3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3+4</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=C4+5<br/><strong>#LOOP</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1+2<br/><strong>#LOOP</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2+3<br/><strong>#LOOP</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3+4<br/><strong>#LOOP</strong></td></tr>
</table>
');

test("Calculations again", '
<table>
<tr><td>SUMA</td><td></td><td>=A2+A3</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1+2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2+C1</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3+C1</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=A2+A3<br/><strong>9.86655</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1+2<br/><strong>11.86655</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2+C1<br/><strong>21.7331</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3+C1<br/><strong>31.59965</strong></td></tr>
</table>
');

test("Some multiplications and divisions",'
<table>
<tr><td>SUMA</td><td></td><td>=A2+A3</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1*2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2/C1</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3-C1</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=A2+A3<br/><strong>9.86655</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1*2<br/><strong>19.7331</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2/C1<br/><strong>2</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3-C1<br/><strong>-7.86655</strong></td></tr>
</table>
');

test("Function: SUM",'
<table>
<tr><td>SUMA</td><td></td><td>=SUM(A2:B4)</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1*2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2/C1</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3-C1</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=SUM(A2:B4)<br/><strong>35.6498944</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1*2<br/><strong>71.2997888</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2/C1<br/><strong>2</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3-C1<br/><strong>-33.6498944</strong></td></tr>
</table>
');

test("Function: SUM with some formulas",'
<table>
<tr><td>SUMA</td><td></td><td>=SUM(A2:B4) + A3 - B4 * B2</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1*2</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2/C1</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3-C1</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=SUM(A2:B4)+A3-B4*B2<br/><strong>8.1530944</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>=C1*2<br/><strong>16.3061888</strong></td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=C2/C1<br/><strong>2</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>=C3-C1<br/><strong>-6.1530944</strong></td></tr>
</table>
');


test("Function: AVG",'
<table>
<tr><td>SUMA</td><td></td><td>=AVG(A2:B4)</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=AVG(A2:B4)<br/><strong>5.9416490666667</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
');


test("Function: Out of range",'
<table>
<tr><td>SUMA</td><td></td><td>=SUM(A2:F4)</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=SUM(A2:F4)<br/><strong>#RANGE</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
');

test("Function: NAN",'
<table>
<tr><td>SUMA</td><td></td><td>=SUM(A1:C4)</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=SUM(A1:C4)<br/><strong>#NAN</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
');


test("Function: Loop detection",'
<table>
<tr><td>SUMA</td><td></td><td>=SUM(C1:C4)</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>0</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>3</td></tr>
</table>
','
<table>
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tr><td>SUMA</td><td></td><td>=SUM(C1:C4)<br/><strong>#LOOP</strong></td></tr>
<tr><td>3,43335</td><td>4.5</td><td>0</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>=A3+B3<br/><strong>13.6432</strong></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>3</td></tr>
</table>
');

test("Evernote code",'
<table style="border-collapse: collapse; table-layout: fixed;" border="1" width="100%" cellspacing="0" cellpadding="2">
<tbody>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">1</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">2</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A1/B2</td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">3</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">4</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A2-B2</td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">5</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">6</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A3*B3</td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">7</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">8</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=SUM(A1:B5)</td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">9</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">10</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A5+B5+C1+C2+C3+C4</td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;"><br clear="none"></td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;"><br clear="none"></td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;"><br clear="none"></td></tr>
</tbody>
</table>
','
<table style="border-collapse: collapse; table-layout: fixed;" border="1" width="100%" cellspacing="0" cellpadding="2">
<thead><tr><td>A</td><td>B</td><td>C</td></tr></thead>

<tbody>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">1</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">2</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A1/B2<br/><strong>0.25</strong></td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">3</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">4</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A2-B2<br/><strong>-1</strong></td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">5</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">6</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A3*B3<br/><strong>30</strong></td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">7</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">8</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=SUM(A1:B5)<br/><strong>55</strong></td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">9</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">10</td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;">=A5+B5+C1+C2+C3+C4<br/><strong>103.25</strong></td></tr>
<tr><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;"><br clear="none"></td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;"><br clear="none"></td><td colspan="1" rowspan="1" valign="top" style="padding: 10.0px; margin: 0.0px; border: 1.0px solid #d9d9d9;"><br clear="none"></td></tr>
</tbody>
</table>
');



