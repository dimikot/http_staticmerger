--TEST--
HTTP_StaticMerger: insecure CSS outside DOCUMENT_ROOT reference
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array('insecure/a.css'));
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/css
/**** insecure/a.css ****/


/**** @import "../../../x.css"; at /insecure/ ****/
/**** ../../../x.css ****/
/* insecure URI */

