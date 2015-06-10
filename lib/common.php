<?php

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'pwTools/string/encoding.php';
require_once INC_PATH.'pwTools/string/StringTools.php';
require_once INC_PATH.'pwTools/debug/TestingTools.php';
require_once INC_PATH.'pwTools/wiki/WikiID.php';

function pw_wiki_getid() {
	$id = ":".WIKINSDEFAULTPAGE;
	if(isset($_GET['id']) && $_GET['id'] != "") {
		$id = $_GET['id'];
	}
	return new WikiID($id);
}

function pw_wiki_getmode() {
	return isset($_GET['mode']) ? $_GET['mode'] : null;
}

function pw_wiki_getcfg($what = "", $subcat = "") {
	
	if (!is_array($_SESSION['pw_wiki'])) {
		throw new Exception("Session pw_wiki is not an ARRAY!");
	}

	if ($what == "")
		return $_SESSION['pw_wiki'];

	if (!array_key_exists($what, $_SESSION['pw_wiki'])) {
		throw new Exception("Session pw_wiki has no category '$what'!");
	}

	if ($subcat == "")
		return $_SESSION['pw_wiki'][$what];

	if (!array_key_exists($subcat, $_SESSION['pw_wiki'][$what])) {
		throw new Exception("Session pw_wiki has no sub-category '$subcat' within '$what'!");
	}

	return $_SESSION['pw_wiki'][$what][$subcat];
}

function pw_wiki_setcfg($key, $value) {
	$_SESSION['pw_wiki'][$key] = $value;
}

function pw_wiki_unsetcfg($key = "") {
	if($key == "") {
		unset($_SESSION['pw_wiki']);
	} else {
		unset($_SESSION['pw_wiki'][$key]);
	}
}

function pw_wiki_loadconfig() {
	
	ini_set('auto_detect_line_endings', false);
	
	if (! isset($_SESSION['pw_wiki'])) {
		global $WIKIDEFAULTCONFIG;
		$config  = $WIKIDEFAULTCONFIG;
		if (isset($_SESSION['pw_wiki']) && is_array($_SESSION['pw_wiki'])) {
			$_SESSION['pw_wiki'] = array_merge($_SESSION['pw_wiki'], $config);
		} else {
			$_SESSION['pw_wiki'] = $config;
		}
	}
}



function pw_wiki_getmenu($id, $mode, Collection $modules) {
	$o = "";
	foreach ($modules->getArray() as $module) {
		if($module instanceof MenuItemProvider && $module->getMenuAvailability()) {
			if(!($module instanceof PermissionProvider) || $module instanceof PermissionProvider && $module->permissionGranted()) {
				$o .= GuiTools::textButton($module->getMenuText(), "id=".$id->getIDAsUrl()."&mode=".$module->getName());
				$o .= " | ";
			}
		}
	}

	return $o;
}




function pw_wiki_file2editor($data) {
	$data = FileTools::setTextFileFormat($data, new TextFileFormat(TextFileFormat::UNIX));
	$data = pw_s2e($data);
	return $data;
}


//TODO Put pw_wiki_getfulltitle to WikiID, getFullPath or similar... 
//TODO WikiID: add getRealPageName that returns also WIKINSDEFAULTPAGEs etc. correctly!
function pw_wiki_getfulltitle($sep = " &laquo; ") {
	$id = pw_wiki_getid();
	$title = "";
	foreach ($id->getFullNSAsArray() as $namespace) { 
		$title = pw_s2e(utf8_ucfirst($namespace)).$sep.$title;
	}
	
	if(!$id->isNS() && $id->getPage() != WIKINSDEFAULTPAGE) {
		$title = pw_s2e(utf8_ucfirst($id->getPage())).$sep.$title;
	}
	
	$title .= pw_s2e(utf8_ucfirst(pw_wiki_getcfg('wikititle')));
	return $title;
}

function pw_wiki_trace(WikiID $id, $sep = "&raquo;") {
	
	$ns = $id->getFullNS();

	$sep = ' '.$sep.' ';

	$ns = preg_split("#:#", $ns, null, PREG_SPLIT_NO_EMPTY);
	$o = "<a href='?mode=showpages&id='>Home</a>";

	$p = "";
	foreach ($ns as $i) {
		$p .= pw_s2url($i).":";
		$i = pw_s2e(utf8_trim($i));
		$i = utf8_ucfirst($i);
		$o .= $sep."<a href='?mode=showpages&id=".$p."'>".$i."</a>";
	}

	return $o;

}

function pw_wiki_syntaxerr($text, $line, $errtxt, $header = 0, $footer = 0) {
	$texp = explode("\n", $text);
	array_pop($texp);
	array_shift($texp);

	$footer = count($texp)-$footer+1;

	$out = "";
	$lnr = 0;
	foreach ($texp as $ltxt) {
		$lnr++;
		if ($lnr == $line) {
			$out .= sprintf("<span style='color: red'>%6s| %s</span><span class='syntaxerror'>&lt; %s</span>\n", $lnr, htmlentities($ltxt), $errtxt);
		} else {
			if ($lnr <= $header or $lnr >= $footer) {
				$out .= sprintf("<span style='color: gray'>%6s| %s</span>\n", $lnr, htmlentities($ltxt));
			} else {
				$out .= sprintf("%6s| %s\n", $lnr, htmlentities($ltxt));
			}

		}
	}
	return $out;
}

function nop($txt) {
	$out = "<span style='color: yellow'>[WARNING: ";
	$out .= $txt;
	$out .= "]</span>";

	return $out;

}


?>