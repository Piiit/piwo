<?php

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'pwTools/parser/LexerRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRule.php';
require_once INC_PATH.'pwTools/parser/Pattern.php';

class InternalLink extends ParserRule implements ParserRuleHandler, LexerRuleHandler {
	
	const MODITEXT = "edit|showpages";
	private static $anchorText = array(
			"_top" 		  => "Page Top",
	        "_toc"        => "Content",	                                               
			"_maintitle"  => "Title",	                                                
			"_bottom"     => "Page Bottom"
			);
	
	public function getName() {
		return strtolower(__CLASS__);
	}
	
	public function onEntry() {
		$indextable = $this->getParser()->getUserInfo('indextable');
		$node = $this->getNode();

		$linkPositionNode = $node->getFirstChild();
		$linkPositionText = $this->getTextFromNode($linkPositionNode);

		$curID = pw_wiki_getid();
		if($linkPositionText[0] == ':') {
			$id = new WikiID($linkPositionText);
		} elseif($linkPositionText[0] == '#') {
			$id = new WikiID($curID->getID().$linkPositionText);
		} else {
			$id = new WikiID($curID->getFullNS().$linkPositionText);
		}
		
		// Find manually set modes like edit or showpages...
		$linkModus = null;
		if($linkPositionNode->getFirstChild()->getName() == 'internallinkmode') {
			$linkModusData = $linkPositionNode->getFirstChild()->getData();
			$linkModus = $linkModusData[0];
			$modi = explode("|", self::MODITEXT);
			if (!in_array($linkModus, $modi)) {
				return nop("Interner Link mit falschem Modus '$linkModus'. Erlaubte Modi sind: ".self::MODITEXT);
			}
		}
		
		//@TODO: refactor... common function... bubble-up of an error until ????
		if ($_SESSION['pw_wiki']['error']) {
			$_SESSION['pw_wiki']['error'] = false;
			return $linkPositionText.nop("Interner Link kann wegen interner Fehler nicht aufgel&ouml;st werden.");
		}
	
		if (!$linkPositionText) {
			return nop("Interner Wikilink ohne Zielangabe. Leerer Wikilink?", false);
		}
	
		$text = null;
		$textNode = $linkPositionNode->getNextSibling();
		if ($textNode != null) {
			$text = $this->getTextFromNode($textNode);
		}
// 		TestingTools::inform($text, "link text");
	
		$found = true;
		$jump = null;
		
		if ($id->hasAnchor()) {
	
			switch($id->getAnchor()) {
				case "_top": 
					$jump = "#__main";
				break;
				case "_bottom": 
					$jump = "#__bottom"; 
				break;
				case "_toc": 
					$jump = "#__toc"; 
				break;
				case "_maintitle": 
					$jump = "#__fullsite"; 
				break;
			}
			
			try {
				// To trigger an exception, also if a text is given...
				$tmp = self::$anchorText[$id->getAnchor()];
				if(!$text) {
					$text = $tmp;
				}
				$found = true;
			} catch (Exception $e) {
				try {
					$item = $indextable->getByIdOrText(pw_url2t($id->getAnchor()));
					$jump = "#header_".$item->getId();
					if(!$text) {
						$text = pw_s2e($item->getText());
					}
					$found = true;
				} catch (Exception $e) {
					$found = false;
					if(!$text) {
						$text = utf8_ucfirst(pw_url2e($id->getAnchor()));
					}
				}
			}
		} 

		$filename = WIKISTORAGE.$id->getPath().WIKIFILEEXT;;
		if (!file_exists($filename) && !$linkModus) {
			$linkModus = "edit";
			$found = false;
		}
		
		if (!$text) {
			$text = utf8_ucfirst($id->getPageAsString());
		}

		$href = "?id=".pw_s2url($id->getID());
	
		if (!$id->hasAnchor()) {
			if ($linkModus == "edit" or !$found) {
				$href .= '&mode=editpage';
			}
			if ($linkModus == "showpages") {
				$href .= "&mode=showpages";
			}
		}
	
		return '<a href="'.$href.$jump.'"'.($found ? "" : ' class="pw_wiki_link_na"').'>'.$text.'</a>';
	}

	public function onExit() {
		return '';
	}

	public function doRecursion() {
		return false;
	}

	public function getPattern() {
		return new Pattern($this->getName(), Pattern::TYPE_SECTION, '(?=\[\[)', '\]\]');
	}
	
	public function getAllowedModes() {
		return array(
				"#DOCUMENT", "tablecell", "listitem", "multiline", "bold", "underline", 
				"italic", "monospace", "small", "big", "strike", "sub", "sup", "hi", "lo", 
				"em", "bordererror", "borderinfo", "borderwarning", "bordersuccess", "bordervalidation", "border", 
				"tablecell", "tableheader", "wptableheader", "wptablecell", "align", 
				"justify", "alignintable", "indent", "left", "right", "footnote", "defitem", "defterm");
	}
}

?>