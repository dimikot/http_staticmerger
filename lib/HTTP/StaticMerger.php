<?php
/**
 * Simple and quite fast static file merger.
 * Used to merge CSS and JS files together to lower the number of client connections.
 *
 * @version 2.34
 */
class HTTP_StaticMerger
{
    /**
     * Separator to be used to glue URIs. We cannot use "!!", because it is unsafe 
     * in HTML comments, so we use "**". See http://www.rfc-editor.org/rfc/rfc1738.txt -
     * "only alphanumerics, the special characters "$-_.+!*'(),", and
     * reserved characters used for their reserved purposes may be used
     * unencoded within a URL"
     */
    const SEPARATOR = '!!';

    /**
     * 2. Replacements to convert base64 encoded string to safe URL.
     */
    private $_safeBase64 = array(array('+', '/'), array('_', '~'));

    private $_maxDate = null;
    private $_type = null;
    private $_base = null;
    private $_secret = null;
    private $_noCompression =  false;

    /**
     * Create a new merger object.
     *
     * @param string $secret         Key for digital signification.
     * @param string $documentRoot   Document root (if non-default used).
     * @param string $baseUri        Base URI which is used to search files.
     * @param bool $noCompression    If true, no URL compression is used.
     */
    public function __construct($secret, $documentRoot = null, $baseUri = '/', $noCompression = false)
    {
        if (strlen($secret) < 4 || false !== strpos($secret, '/') || false !== strpos($secret, '\\')) {
            throw new Exception("HTTP_StaticMerger::__construct(): till 2.0 the first parameter is a digital signature secret. It should be larger than 4 characters and contan no \"/\" and \"\\\" within.");
            return;
        }
        if (!$documentRoot) {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        }
        $this->_noCompression = $noCompression;
        $this->_secret = $secret;
        $this->_documentRoot = realpath($documentRoot);
        $this->_base = $baseUri;
    }

    /**
     * Merger handler entry point.
     *
     * @param string $charset  If set, add this charset to Content-type header.
     * @return void
     */
    public function main($charset = null)
    {
        // Parse the URI.
        $uriList = $this->_requestToList($_SERVER['REQUEST_URI']);
        if ($uriList === null) {
            // Digital signature is incorrect.
            $this->_header("HTTP/1.1 404 Not Found");
            $this->_header("Content-type: text/plain");
            echo "Malformed merged URL!\n";
            return false;
        }
        // URI is OK, glue files.
        $merged = $this->_mergeUris($uriList);
        $content = $merged['content'];
        $maxDate = $merged['maxDate'];
        $mime = $merged['mime'];
        $etag = md5($content);
        // It's safe to cache this URL forever, because URL depends on files timestamps.
        // ATTENTION! 0x7F000000 is the maximum, else nginx treats it as no-cache.
		$this->_header("Expires: " . gmdate("D, d M Y H:i:s", 0x7F000000) . " GMT");
        $this->_header("Cache-Control: public, max-age=29030400");
        // Process Last-Modified and e-tag.
        $this->_header("Last-Modified: " . gmdate("D, d M Y H:i:s", $maxDate) . " GMT");
        $this->_header("Etag: " . $etag);
        if (@strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) === $maxDate || trim(@$_SERVER["HTTP_IF_NONE_MATCH"]) === $etag) {
            $this->_header("HTTP/1.1 304 Not Modified");
            return;
        }
        $this->_header("Content-type: " . ($mime? $mime : 'text/plain') . ($charset? "; charset=$charset" : ""));
        if (preg_match('/^utf-?8$/is', $charset)) {
            $content = str_replace(chr(239) . chr(187) . chr(191), '', $content); // remove BOM
        }
        echo $content;
        return true;
    }

    /**
     * Return piece of HTML for specified URIs to merge.
     *
     * @param string $handler  URL of merger script.
     * @param array $uriList   List of URIs to be merged.
     * @param $noMerge         If true, each URI is included in its
     *                         own tag (useful for JS/CSS debugging).
     * @param $additionalAttr  If passed, this string is added to tag attributes.
     * @return string
     */
    public function getHtml($handler, $uriList, $noMerge = false, $additionalAttr = '')
    {
        // Detect MIME type.
        $mime = null;
        foreach ($uriList as $uri) {
            $type = $this->_getUriMime($uri);
            if ($type) {
                $mime = $type;
                break;
            }
        }
        // Build the list of URIs to be wrapped with tags.
        if ($noMerge) {
            $uris = array();
            foreach ($uriList as $uri) {
                $uri = $this->_rel2abs($uri, $handler);
                $uri .= (false === strpos($uri, "?")? '?' : "&") . filemtime($this->_uri2fname($uri));
		        if ($handler && preg_match('{^((\w+:)?//[^/]+)}s', $handler, $m)) {
		            // If merger handler has a domain, also add this domain to URI.
		            $uri = $m[1] . $uri;
		        }
                $uris[] = $uri; 
            }
        } else {
        	// If some of URIs are absolute, render them in separate tags
        	// AT THE END of merged CSSes/JSes.
        	$absUrls = array();
        	foreach ($uriList as $i => $uri) {
        		if (preg_match('{^((\w+:)?//)}s', $uri)) {
        			$absUrls[] = $uri;
        			unset($uriList[$i]);
        		}
        	}
          	$uris = array_merge(
          		array($this->_listToRequest($handler, array_values($uriList))), 
          		$absUrls
          	);
        }
        // Build tags.
        $tags = array();
        foreach ($uris as $uri) {
            if ($mime == 'text/css') {
                $tags[] = '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($uri) . '"' . ($additionalAttr? ' ' . $additionalAttr : '') . ' />';
            } else {
                // &#160; is for XHTML.
                // IE6 supports only text/javascript!
                $tags[] = '<script type="text/javascript" src="' . htmlspecialchars($uri) . '"' . ($additionalAttr? ' ' . $additionalAttr : '') . '>&#160;</script>';
            }
        }
        return join("\n", $tags);
    }

    /**
     * Send a HTTP header.
     *
     * @return void
     */
    protected function _header($s)
    {
        header($s);
    }

    /**
     * Returns file mtime (may be overriden in tests).
     *
     * @param string $fila
     * @return int
     */
    protected function _filemtime($file)
    {
    	return filemtime($file);
    }
    
    /**
     * Merge content of specified URIs and return the result in form of:
     * array('content' => ..., 'maxDate' => ..., 'mime' => '')
     *
     * @param array $uris
     * @return array
     */
    private function _mergeUris($uris)
    {
        $this->_maxDate = filemtime(__FILE__);
        $this->_type = null;
        $content = $this->_mergeUrisInternal($uris);
        return array(
            'content' => $content,
            'maxDate' => $this->_maxDate,
            'mime'    => $this->_type,
        );
    }

    /**
     * URL formats:
     * a) For uncompressed URLs:
     *    <handler> -- <signature> -- <timestamp> -- <e1> -- <e2> -- ...
     * b) For compressed URLs:
     *    <handler> -- <signature> -- -- <base64_compressed>
     */

    /**
     * Return the list of URIs for main() method.
     * Commonly URI is brought from REQUEST_URI in form of:
     * /path/to/merger--hash--timestamp--uri1--uri2--uri3
     * URIs to merge are after the first "--" in this URL.
     *
     * @param string $requestUri
     * @return array  Return null if the signature is incorrect.
     */
    private function _requestToList($requestUri)
    {
        $exploded = explode(self::SEPARATOR, $requestUri, 3);
        // Strip the script name (before first "--").
        // Note that the signature DEPENDS on prefix - to avoid hacks.
        $handler = array_shift($exploded);
        // Extract hashcode.
        $signature = $exploded? array_shift($exploded) : '';
        // Extract merged data.
        $merged =  $exploded? array_shift($exploded) : '';
        // Check that the signature is correct.
        if ($this->_hash($handler, $merged) !== $signature) {
            return null;
        }
        // Uncompress if needed.
        $merged = $this->_uncompress($merged);
        // Explode by separator.
        $parts = explode(self::SEPARATOR, $merged);
        // Extract timestamp (if present).
        if ($parts && is_numeric($parts[0])) {
            // Strip optional first part - modification timestamp.
            array_shift($parts);
        }
        return $parts;
    }

    /**
     * Build REQUEST_URI based on merger handler and URI list to be merged.
     * Maximum URI timestamp is added to this URI to make browser caching better.
     *
     * @param string $handler
     * @param array $list
     * @return string
     */
    private function _listToRequest($handler, $list)
    {
        $mtime = filemtime(__FILE__);
        foreach ($list as $uri) {
            $stat = $this->_getUriStat($uri);
            if ($stat) {
                $mtime = max($stat['mtime'], $mtime);
            }
        }
        // Insert mtime as the first list element.
        array_unshift($list, strval($mtime));
        // ATTENTION!
        // Add mtime AT THE START of URL, because we need this URL to be ended
        // by a filename with extension (for proper content-type detection by
        // caching proxy like nginx).
        $merged = join(self::SEPARATOR, $list);
        $compressed = $this->_compress($merged);
        $signature = $this->_hash($handler, $compressed);
        return $handler . self::SEPARATOR . $signature . self::SEPARATOR . $compressed;
    }

    private function _compress($s)
    {
        if (!function_exists('gzdeflate') || $this->_noCompression) {
            return $s;
        }
        // Determine     extension if we use compressed version.
        $ext = preg_match('/\.(\w+)$/s', $s, $m)? '.' . $m[1] : '';
        // Post-process (replace dangerous symbols and add slashes to avoid long filenames).
        $s = base64_encode(gzdeflate($s));
        $s = str_replace($this->_safeBase64[0], $this->_safeBase64[1], $s);
        $s = join("/", str_split($s, 80));
        // Empty element between separators means compressed content.
        return self::SEPARATOR . $s . $ext;
    }

    private function _uncompress($s)
    {
        if (!function_exists('gzinflate')) {
            return $s;
        }
        if (substr($s, 0, strlen(self::SEPARATOR)) === self::SEPARATOR) {
            $s = substr($s, strlen(self::SEPARATOR));
            // Strip file extension from the compressed URL.
            $s = preg_replace('/\.(\w+)$/s', '', $s);
            // Pre-process (replace back dangerous symbols and remove slashes).
            $s = str_replace('/', '', $s);
            $s = str_replace($this->_safeBase64[1], $this->_safeBase64[0], $s);
            // Empty element between separators means compressed content.
            return @gzinflate(base64_decode($s));
        }
        return $s;
    }
    
    /**
     * Calculate digital signature.
     * 
     * @param string $handler    URI prefix before the signature (handler name).
     * @param array $compressed  URLs data.
     * @return string
     */
    private function _hash($handler, $compressed)
    {
        return md5($this->_secret . $this->_getHandlerPath($handler) . $compressed);
    }
    
    /**
     * Extracts path from handler if it is an absolute URL
     * 
     * @param string $handler
     * @return string
     */
    private function _getHandlerPath($handler)
    {
    	// handling protocol-less urls
    	if (substr($handler, 0, 2) == '//') {
    	   $handler = "protocol:" . $handler; 
    	}
    	return parse_url($handler, PHP_URL_PATH) . (false !== strpos($handler, '?')? '?' . parse_url($handler, PHP_URL_QUERY) : '');
    }

    /**
     * Return creation timestamp of a specified URI and its filename.
     * Return null for insecure URIs.
     *
     * @param string $uri
     * @return array('fname' => filename, 'mtime' => modification_time)
     */
    private function _getUriStat($uri)
    {
        $fname = $this->_uri2fname($uri);
        if (!$fname) {
            return null;
        }
        return array(
            'fname' => $fname,
            'mtime' => @filemtime($fname),
        );
    }

    /**
     * Merge a list of URIs and return merged results.
     *
     * @param array $uris
     */
    private function _mergeUrisInternal($uris, $checkSecurity = true)
    {
        $content = array();
        foreach ($uris as $uri) {
            if (!strlen(trim($uri))) continue;
            $content[] = "/**** $uri ****/";
            if ($checkSecurity) {
                $type = $this->_getUriMime($uri);
                if (!$type) {
                    $content[] = "/* invalid URI format */";
                    continue;
                }
                $this->_type = $type;
            }
            $stat = $this->_getUriStat($uri);
            if (!$stat) {
                $content[] = "/* insecure URI */";
                continue;
            }
            $mtime = $stat['mtime'];
            $fname = $stat['fname'];
            if (!$mtime) {
                continue;
            }
            $data = @file_get_contents($fname);
            if ($this->_type == "text/css") {
                $data = $this->_processCss($data, $uri);
            }
            $this->_maxDate = max($this->_maxDate, $mtime);
            $content[] = $data . "\n" . ($this->_type == "text/css"? "" : ";") . "\n";
        }
        return join("\n", $content);
    }

    /**
     * Return URI MIME type or null of URI has invalid (e.g. insecure) format.
     *
     * @param string $uri
     * @return string
     */
    private function _getUriMime($uri)
    {
        if (!preg_match('{^(?:[-\w/]+|\.(?=[^./]))*\.(css|js)$}s', $uri, $m)) {
            return null;
        }
        return $m[1] == 'css'? 'text/css' : 'application/x-javascript';
    }

    /**
     * Convert relative URI to absolute.
     *
     * @param string $uri      URI to absolutize.
     * @return string
     */
    private function _rel2abs($uri)
    {
        if (substr($uri, 0, 1) != '/') {
            $uri = $this->_base . (substr($this->_base, -1) == '/'? '' : '/') . $uri;
        }
        return $uri;
    }

    /**
     * Convert relative URI to filename.
     * If the URI is insecure (e.g. points outside DOCUMENT_ROOT), return null.
     *
     * @param string $uri
     * @return string
     */
    private function _uri2fname($uri)
    {
        $uri = $this->_rel2abs($uri);
        $fname = $this->_documentRoot . (substr($uri, 0, 1) == '/'? '' : '/') . $uri;
        $realpath = @realpath($fname);
        if (!$realpath || substr($realpath, 0, strlen($this->_documentRoot)) !== $this->_documentRoot) {
            return null;
        }
        return $realpath;
    }

    /**
     * Process @import directives and remove comments in a CSS file.
     *
     * @param string $data  CSS data.
     * @param string $uri   URI from which this CSS is loaded
     * @return string       Expanded result.
     */
    private function _processCss($data, $uri)
    {
        $data = preg_replace('{/\* .*? \*/}xs', '', $data);
        if (false === strpos($data, "@import")) {
            return $data;
        }
        $prevBase = $this->_base;
        $this->_base = preg_replace('{[^/]+$}s', '', $this->_rel2abs($uri));
        $result = preg_replace_callback('/
                @import (?:
                    \s+ url \s* \( \s* ["\']? ([^"\'()]+) ["\']? \s* \)
                    |
                    \s+ ["\']? ([^"\';()]+) ["\']?
                ) \s* ;
            /sxi',
            array($this, '_processImportsCallback'),
            $data
        );
        $this->_base = $prevBase;
        return $result;
    }

    /**
     * Callback for preg_replace_callback().
     * Must be public.
     *
     * @param array $m
     * @return string
     */
    public function _processImportsCallback($m)
    {
        $uri = strlen($m[1])? $m[1] : $m[2];
        if (preg_match('{^\w+://}s', $uri)) {
            return $m[0];
        }
        return "\n\n/**** {$m[0]} at {$this->_base} ****/\n" . rtrim($this->_mergeUrisInternal(array($uri), false)) . "\n";
    }
}
