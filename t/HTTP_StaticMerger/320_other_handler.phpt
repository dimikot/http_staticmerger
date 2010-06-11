--TEST--
HTTP_StaticMerger: malformed handler
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = str_replace('/merger/', '/other/', getUri(array('a.css', 'b.css')));
echo $_SERVER['REQUEST_URI'] . "\n";
$merger->main();

?>

--EXPECTF--
/other/!!%s
HTTP/1.1 404 Not Found
Content-type: text/plain
Malformed merged URL!
