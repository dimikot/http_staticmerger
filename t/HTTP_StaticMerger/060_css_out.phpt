--TEST--
HTTP_StaticMerger: CSS with another site URL
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array('out.css'));
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/css
/**** out.css ****/
content_of_out {}

@import url(http://www.ru/x.css);
