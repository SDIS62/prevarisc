<?php
/**
 * Configuration for "min", the default application built with the Minify
 * library
 */

$min_enableBuilder = false;
$min_builderPassword = '';
$min_errorLogger = getenv('PREVARISC_DEBUG_ENABLED') ? true : false;
$min_allowDebugFlag = getenv('PREVARISC_DEBUG_ENABLED') ? true : false;
$min_documentRoot = '';
$min_cacheFileLocking = true;
$min_serveOptions['bubbleCssImports'] = false;
$min_serveOptions['maxAge'] = 1800;
$min_serveOptions['minApp']['groupsOnly'] = false;
$min_symlinks = array();
$min_uploaderHoursBehind = 0;
$min_libPath = dirname(__FILE__) . '/lib';

ini_set('zlib.output_compression', '0');
