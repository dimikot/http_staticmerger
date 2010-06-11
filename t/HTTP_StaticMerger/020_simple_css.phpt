--TEST--
HTTP_StaticMerger: simple CSS test
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array('a.css', 'b.css'));
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/css
/**** a.css ****/
content_of_a {}



/**** b.css ****/
content_of_b {}

