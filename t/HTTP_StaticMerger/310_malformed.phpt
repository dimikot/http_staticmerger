--TEST--
HTTP_StaticMerger: malformed URL
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = 'ahahahah';
$merger->main();

--EXPECT--
HTTP/1.1 404 Not Found
Content-type: text/plain
Malformed merged URL!
