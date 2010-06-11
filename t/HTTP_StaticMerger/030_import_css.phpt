--TEST--
HTTP_StaticMerger: CSS with @import
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER['REQUEST_URI'] = getUri(array('a.css', 'c.css', 'b.css'));
$merger->main();

?>

--EXPECT--
Last-Modified: ***
Etag: ***
Content-type: text/css
/**** a.css ****/
content_of_a {}



/**** c.css ****/
content_of_c {}



/**** @import url(a.css); at / ****/
/**** a.css ****/
content_of_a {}



/**** @import url("/dir/m.css"); at / ****/
/**** /dir/m.css ****/
content_of_m {}



/**** @import "folder/n.css"; at /dir/ ****/
/**** folder/n.css ****/
content_of_n {}



/**** @import url(b.css); at / ****/
/**** b.css ****/
content_of_b {}




/**** b.css ****/
content_of_b {}

