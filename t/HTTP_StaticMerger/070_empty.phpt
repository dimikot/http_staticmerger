--TEST--
HTTP_StaticMerger: empty URI list
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array());
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/plain

