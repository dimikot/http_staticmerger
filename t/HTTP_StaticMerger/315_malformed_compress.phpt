--TEST--
HTTP_StaticMerger: malformed URL compression
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = substr(getUri(array('a.css', 'b.css')), 0, -10) . "aaaaaaaaaa"; 
echo $_SERVER['REQUEST_URI'] . "\n";
$merger->main();

--EXPECTF--
/merger/!!%s
HTTP/1.1 404 Not Found
Content-type: text/plain
Malformed merged URL!

