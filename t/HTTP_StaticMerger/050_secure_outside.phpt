--TEST--
HTTP_StaticMerger: secure CSS outside reference
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array('insecure/b.css'));
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/css
/**** insecure/b.css ****/


/**** @import "../c.css"; at /insecure/ ****/
/**** ../c.css ****/
content_of_c {}



/**** @import url(a.css); at /insecure/../ ****/
/**** a.css ****/
content_of_a {}



/**** @import url("/dir/m.css"); at /insecure/../ ****/
/**** /dir/m.css ****/
content_of_m {}



/**** @import "folder/n.css"; at /dir/ ****/
/**** folder/n.css ****/
content_of_n {}



/**** @import url(b.css); at /insecure/../ ****/
/**** b.css ****/
content_of_b {}

