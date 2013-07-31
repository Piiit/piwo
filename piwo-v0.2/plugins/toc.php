<?phpif (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../').'/');
}
require_once INC_PATH.'pwTools/data/IndexTable.php';
require_once INC_PATH.'pwTools/data/IndexItem.php';require_once INC_PATH.'pwTools/parser/ParserRule.php';
require_once INC_PATH.'pwTools/parser/TreeParser.php';require_once INC_PATH.'pwTools/tree/Node.php';
function plugin_toc(TreeParser $parser, Node $node) {    global $indextable;    if ($indextable instanceof IndexTable) {		$o	= '<div class="toc" id="__toc">';		$o .= '<ul>';		$lastlvl = 0;		foreach($indextable->getAsArray() as $item) {			if ($lastlvl < $item->getLevel()) {				$diff = $item->getLevel() - $lastlvl;				for ($i = 0; $i < $diff; $i++)					$o .= '<ul>';			} elseif ($lastlvl > $item->getLevel()) {				$diff = $lastlvl - $item->getLevel();				for ($i = 0; $i < $diff; $i++)					$o .= '</ul>';			}			$o .= '<li>';			$o .= '<a href="#header_'.$item->getId().'">'.$item->getId().' '.pw_s2e($item->getText()).'</a>';			$o .= '</li>';			$lastlvl = $item->getLevel();		}		$o .= '</ul>';		$o .= '</div>';		#out($indextable);    }    return $o;  }
function plugin_nstoc(TreeParser $parser, Node $node) {		$token = new ParserRule($node, $parser);	$cont = $token->getArray($node);		// Der erste Parameter gibt den Namensraum an...	$nstxt = array_shift($cont);	$nstxt = utf8_trim($nstxt);	$nstxt = pw_wiki_fullns($nstxt.':');		// Parameter TITLE: Soll ein Titel ausgegeben werden?	$titeltxt = "";	if (in_array("TITLE", $cont)) {			// @TODO: BUG: Falls relative Pfadangaben (wie ..) bestehen muss der Pfad 		// zuerst aufgelöst werden, bevor man den korrekten Titel ermitteln kann.		$nslist = $nstxt;			if ($nslist == "") {			$nslist = "[root]";			$nstxt = ":";		}			//@TODO: remove ending : with regular expression if there are more than one!!!		if ($nslist[strlen($nslist)-1] == ':') {			$nslist = substr($nslist, 0, -1);		}			$titeltxt = utf8_ucwords(str_replace(":", " &raquo; ", $nslist));		$titeltxt = "Inhalt des Namensraumes <i>\"$titeltxt\"</i>: ";	}	// Parameter NOERR: Fehlermeldungen unterdrücken?	$error = true;	if (in_array("NOERR", $cont)) {		$error = false;	}	// Parameter SHOWNS: Zeige auch die untergeordneten Namensräume...	$showns = false;	if (in_array("SHOWNS", $cont)) {		$showns = true;	}	return pw_wiki_nstoc($nstxt, $titeltxt, $error, $showns);}function pw_wiki_nstoc($ns, $titel, $error, $showns) {    $ext = "";  $o = "";  $glob_flag = 0;  if (!$showns) {    $ext = pw_wiki_getcfg('fileext');  } else {    $glob_flag = GLOB_ONLYDIR;  }    $path = pw_wiki_getcfg('path');  if ($ns) {    $path = pw_wiki_path($ns, ST_NOEXT);  }     $wikis = glob($path."/*".$ext, $glob_flag);  // Titel werden nur ausgegeben, wenn Fehlermeldungen auch ausgegeben werden dürfen!  // ...sonst kann es zu alleinstehenden Titeln kommen.  if (utf8_strlen($titel) > 0 and $error) {    $o .= $titel;  }  if($error and empty($wikis))    return $o."<br />".nop("Es sind keine Texte im Namensraum '".pw_s2e($ns)."' vorhanden.", false);  $o .= "<ul>";  foreach($wikis as $i) {    $page = pw_basename($i, ".txt");    $page = utf8_ucfirst($page);    $page = pw_s2e($page);    $id = pw_wiki_path2id($i);    $o .= "<li><a href='?id=$id'>".$page."</a></li>";  }  $o .= "</ul>";  return $o;}
function createindextable(TreeParser $parser, Node $node, IndexTable $indextable = null) {	if($node->hasChildren() && $node->getName() != "notoc") {		for ($node = $node->getFirstChild(); $node != null; $node = $node->getNextSibling()) {	    	if ($node->getName() == "header") {	    		$token = new ParserRule($node, $parser);		    	$text = utf8_trim(pw_e2u($token->getText($node)));		    	$config = $node->getData();	      		$level = utf8_strlen($config[0]);	      		$indextable->add($level, $text);	    	}	    	//if ($node->hasChildren() && $node->getName() != "notoc") {	        	createindextable($parser, $node, $indextable);	    	//}	  	}	}}function plugin_trace (TreeParser $parser, Node $node, $sep = "&raquo;") {  $sep = ' '.$sep.' ';  $id = pw_wiki_getcfg('id');  $mode = pw_wiki_getcfg('mode');  $startpage = pw_wiki_getcfg('startpage');  #out($id);  $o = "<a href='?mode=cleared&id=".pw_s2url($startpage)."'>Home</a>";  $fullpath = explode(":", $id);  $pg = array_pop($fullpath);  $p = "";  foreach ($fullpath as $i) {    $p .= "$i:";    $i = pw_url2u($i);    $i = utf8_ucfirst($i);    $i = pw_s2e($i);    $o .= $sep."<a href='?mode=cleared&id=".rtrim($p, ":")."'>".$i."</a>";  }  $pg = pw_url2u($pg);  $pg = utf8_ucfirst($pg);  $pg = pw_s2e($pg);  $o .= $sep.$pg;  return $o;}?>