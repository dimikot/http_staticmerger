HTTP_StaticMerger: Automatical "merging" of CSS and JS files for faster load
(C) Dmitry Koterov, http://en.dklab.ru/lib/HTTP_StaticMerger/

The library HTTP_StaticMerger merges "on the air" a set of static files (CSS 
or JS) and speedups page loading (lower number of HTTP queries). The nearest 
analog is minify: http://code.google.com/p/minify/ , but HTTP_StaticMerger
purpose is to do "on the air" and very fast merge with no intermediate
steps. It is much more transparent for a developer.

Of course, we recommend to use the library together with caching reverse-proxy 
(e.g. nginx) to minimize response times. 

Please see datailed documentation at
http://en.dklab.ru/lib/HTTP_StaticMerger/
