--TEST--
HTTP_StaticMerger: Manual charset specification
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array('out.css'));
$merger->main("utf-8");

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/css; charset=utf-8
/**** out.css ****/
content_of_out {}

@import url(http://www.ru/x.css);

