<?phpif (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'piwo-v0.2/lib/common.php';require_once INC_PATH.'pwTools/string/encoding.php';require_once INC_PATH.'pwTools/debug/TestingTools.php';require_once INC_PATH.'pwTools/file/FileTools.php';
require_once INC_PATH.'pwTools/gui/GuiTools.php';


function pw_wiki_update_cache($forced = false) {	$files = new RecursiveIteratorIterator(				new RecursiveDirectoryIterator(WIKISTORAGE)			 );
	foreach($files as $filename) {		if(substr($filename, (-1) * strlen(WIKIFILEEXT)) == WIKIFILEEXT) {			try {				pw_wiki_get_parsed_file(WikiID::fromPath($filename, WIKISTORAGE, WIKIFILEEXT), $forced);			} catch (Exception $e) {				echo "<pre>Exception: Skipping file '$filename': $e\n</pre>";			}		}
	}}function pw_wiki_get_parsed_file(WikiID $id, $forcedCacheUpdate = false) {		$filename = WIKISTORAGE.$id->getPath().WIKIFILEEXT;	$headerID = new WikiID(WIKITEMPLATESNS."header");	$footerID = new WikiID(WIKITEMPLATESNS."footer");
	$headerFilename = WIKISTORAGE."/".$headerID->getPath().WIKIFILEEXT;;
	$footerFilename = WIKISTORAGE."/".$footerID->getPath().WIKIFILEEXT;;
	
	if (!is_file($filename)) {
		throw new Exception("File '$filename' does not exist!");
	}
	if (!is_file($headerFilename)) {
		throw new Exception("File '$headerFilename' does not exist!");
	}
	if (!is_file($footerFilename)) {
		throw new Exception("File '$footerFilename' does not exist!");
	}		/* 	 * This is only executed with configuration CACHE ENABLED!	 * If the cached file is still up-to-date do nothing, except forced 	 * overwrite is enabled.	 */	if(pw_wiki_getcfg('useCache') == true) {		$cachedFilename = WIKICACHE."/".$id->getPath().WIKICACHEFILEEXT;				if(! $forcedCacheUpdate && is_file($cachedFilename)) {						$cachedFileModTime = filemtime($cachedFilename);			if ($cachedFileModTime >= filemtime($filename) 				&& $cachedFileModTime >= filemtime($headerFilename) 				&& $cachedFileModTime >= filemtime($footerFilename)) {										$data = file_get_contents($cachedFilename);				if ($data === false) {					throw new Exception("Unable to read data file '$cachedFilename'!");				}				TestingTools::inform("Using cached file :".$cachedFilename);				return $data;			}		}	}		$data = file_get_contents($filename);
	if ($data === false) {
		throw new Exception("Unable to read data file '$filename'!");
	}
	$headerData = file_get_contents($headerFilename);
	if ($headerData === false) {
		throw new Exception("Unable to read template file '$headerFilename'!");
	}
	$footerData = file_get_contents($footerFilename);
	if ($footerFilename === false) {
		throw new Exception("Unable to read template file '$footerFilename'!");
	}		$data = $headerData."\n".$data."\n".$footerData;	$data = FileTools::setTextFileFormat($data, new TextFileFormat(TextFileFormat::UNIX));		if (!utf8_check($data)) {		throw new Exception("File '$filename' is not an UTF8-encoded file!");	}		$out = parse($data, pw_wiki_getcfg('debug'));		/*	 * Write parser results to a file if CACHING is enabled. 	 */	if (pw_wiki_getcfg("useCache") == true) {		FileTools::createFolderIfNotExist(dirname($cachedFilename));		if (file_put_contents($cachedFilename, $out) === false) {			throw new Exception("Unable to write file '$cachedFilename'!");		}	}		return $out;}function parse($text, $forse_debug = true) {	// 	$pathToPlugins = INC_PATH."piwo-v0.2/lib/plugins";	//FIXME This are not plugins, but additional user-defined tokens -> rename!	$pathToPlugins = null;	$debugCatchedException = false;	$wikiParser = new WikiParser($pathToPlugins);	$o = "";	$es = null;	try {		$wikiParser->parse($text);		$o = $wikiParser->getResult();	} catch (Exception $e) {		$debugCatchedException = true;		TestingTools::error("Exception catched! ERROR MESSAGE: ".pw_s2e(print_r($e->getMessage(), true)));		TestingTools::error("ERROR TRACE: \n".pw_s2e($e->getTraceAsString()));		$es = $e;	}	if ($debugCatchedException || $forse_debug) {		TestingTools::inform("LEXER: ".$wikiParser->getLexer(), TestingTools::NOTYPEINFO);		TestingTools::debug ( "PATTERN TABLE: \n" . $wikiParser->getLexer ()->getPatternTableAsString () );		$treePrinter = new TreeWalker ( $wikiParser->getLexer ()->getRootNode (), new TreePrinter () );		TestingTools::inform ( "PARSE TREE: \n" . StringTools::showLineNumbers ( $treePrinter->getResult () ) );		TestingTools::inform ( "SPEED: Text parsed in " . $wikiParser->getLexer()->getExecutionTime()." seconds!" );		TestingTools::inform ( "SOURCE:\n" . StringTools::showLineNumbers ( $wikiParser->getSource () ) );		//$debugString .= "<h3>Debug: Parser - Schritte (TODO: ADAPT TO NEW LEXER)</h3>";		//$lexer->printDebugInfo(1,1);		if ($debugCatchedException) {			throw $es;		}	}	return $o;}function pw_wiki_lexerconf(Lexer $lexer) {	$lexer->addWordPattern("newline", '(?<=\n)\n');	$lexer->addSectionPattern("wptable", '\n\{\|', '\|\}');	$lexer->addSectionPattern("wptableline", '\|-', '(?=\|\}|\|-)');	#$lexer->addSectionPattern("wptableheader", '\!\! *|\n\! *', '(?= *\!\!|\n)');	$lexer->addSectionPattern("wptableheader", '(\!\! *|\n\! *)', '(?= *\!\!|\n)'); //ENTRY must be bracketed... otherwise wptable finds its EXIT too early... wrong count!	$lexer->addSectionPattern("wptablecell", '(\|\| *|\n\|(?![\}-]) *)', '(?= *\|\||\n)');	$lexer->addLinePattern("wptabletitle", '\|\+ *');	$lexer->addWordPattern("wptableconfig", '([\w]+) *= *[\"\']?([^\"\']*)[\"\']? *\|* *');	$lexer->connectTo("wptableheader", "wptableline2");	$lexer->connectTo("wptablecell", "wptableline2");	// TODO: Aggregate categories and cleanup, Attention with Deflists and Modi, which must (not) be a part of their selfs...	$blocks = array("#DOCUMENT", "tablecell", "listitem", "multiline");	// 	$tables = array("tablecell", "tableheader", "wptableheader", "wptablecell");	$boxes = array("bordererror", "borderinfo", "borderwarning", "bordersuccess", "bordervalidation", "border");	// 	$format = array("bold", "underline", "italic", "monospace", "small", "big", "strike", "sub", "sup", "hi", "lo", "em");	// 	$align = array("align", "justify", "alignintable", "indent", "left", "right");	$lexer->setAllowedModes("newline", array_merge($blocks, $boxes, array("#DOCUMENT", "multiline")));	$lexer->setAllowedModes("wptable", array("#DOCUMENT", "multiline", "wptablecell"));	$lexer->setAllowedModes("wptableline", array("wptable"));	$lexer->setAllowedModes("wptabletitle", array("wptable"));	$lexer->setAllowedModes("wptableheader", array("wptableline", "wptable"));	$lexer->setAllowedModes("wptablecell", array("wptableline", "wptable"));	$lexer->setAllowedModes("wptableconfig", array("wptable", "wptableline", "wptableheader", "wptablecell", "wptabletitle"));	return $lexer;}
?>