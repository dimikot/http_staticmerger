HTTP_StaticMerger: Automatical "merging" of CSS and JS files for faster load
(C) Dmitry Koterov, http://en.dklab.ru/lib/HTTP_StaticMerger/

The library HTTP_StaticMerger merges "on the air" a set of static files (CSS 
or JS) and speedups page loading (lower number of HTTP queries). The nearest 
analog is minify: http://code.google.com/p/minify/ , but HTTP_StaticMerger
purpose is to do "on the air" and very fast merge with no intermediate
steps. It is much more transparent for a developer.

Of course, we recommend to use the library together with caching reverse-proxy 
(e.g. nginx) to minimize response times. 

When you use the library as:

    <head>
      <?
      $merger = new HTTP_StaticMerger('a-secret-and-constant-string');
      echo $merger->getHtml("/merge.php/", array("js/jquery.js", "js/jquery-dimensions.js", ...));
      echo $merger->getHtml("/merge.php/", array("css/common.css", "css/menu.css", ...));
      ?>
    </head>

it generates a code like:

    <head>
      <script src="/merge.php/!!a2de6253!!12345678!!js/jquery.js!!js/jquery-dimensions.js!!..."></script>
      <link rel="stylesheet" type="text/css" href="/merge.php/!!3ad3b1f8!!12345678!!css/common.css!!css/menu.css!!..."></script>
    </head>

Please see datailed documentation at
http://en.dklab.ru/lib/HTTP_StaticMerger/
