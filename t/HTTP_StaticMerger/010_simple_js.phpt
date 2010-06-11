--TEST--
HTTP_StaticMerger: simple JS test
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array("dir/a.js", "dir/b.js"));
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: application/x-javascript
/**** dir/a.js ****/
function content_of_a() {}

;

/**** dir/b.js ****/
function content_of_b() {}

;

