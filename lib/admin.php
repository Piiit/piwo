<?php
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}

require_once INC_PATH.'pwTools/gui/GuiTools.php';


function pw_wiki_update_cache($forced = false) {
	foreach($files as $filename) {
	}
	}
		throw new Exception("File '$headerFilename' does not exist!"); 
	}
		throw new Exception("File '$footerFilename' does not exist!");
	}
		throw new Exception("Unable to read data file '$filename'!");
	}
	$headerData = file_get_contents($headerFilename);
	if ($headerData === false) {
		throw new Exception("Unable to read template file '$headerFilename'!");
	}
	$footerData = file_get_contents($footerFilename);
	if ($footerFilename === false) {
		throw new Exception("Unable to read template file '$footerFilename'!");
	}
	
	if (!utf8_check($data)) {
		throw new Exception("File '$filename' is not an UTF8-encoded file!");
	}
	
	$out = parse($data, pw_wiki_getcfg('debug'));
	if (file_put_contents($cachedFilename, $out) === false) {
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
	}
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
	}
	$data = FileTools::setTextFileFormat($data, new TextFileFormat(TextFileFormat::UNIX));
function pw_wiki_showcontent(WikiID $id) {
}


?>