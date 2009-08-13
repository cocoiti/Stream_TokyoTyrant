<?php
require "/home/cocoiti/Net_TokyoTyrant/trunk/Net/TokyoTyrant.php";
$tt = new Net_TokyoTyrant();
$tt->connect('localhost', 1978);
$test1 ='<?php $a = 11;';

$tt->put('test1.php', $test1);
$test2 = '<?php $a = 22;';
$tt->put('test2.php', $test2);
$tt->close();


include "http://localhost:1978/test2.php";
assert($a === 22);
require dirname(dirname(__FILE__)) . '/Stream/TokyoTyrant.php';

Stream_TokyoTyrant::register();
include "tokyotyrant://localhost:1978/test1.php";
assert($a === 11);

file_put_contents("tokyotyrant://localhost:1978/test3.php", 'himote');
assert(file_get_contents("tokyotyrant://localhost:1978/test3.php") === 'himote');

