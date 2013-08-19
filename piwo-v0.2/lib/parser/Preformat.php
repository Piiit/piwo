<?php

//TODO $lexer->connectTo("preformat", "pre"); 
// function spre() {
//   return '<pre><div>';
// }

// function epre() {
//   return '</div></pre>';
// }

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'pwTools/parser/LexerRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRule.php';
require_once INC_PATH.'pwTools/parser/Pattern.php';

class Preformat extends ParserRule implements ParserRuleHandler, LexerRuleHandler {
	
	public function getName() {
		return strtolower(__CLASS__);
	}
	
	public function onEntry() {
		return '';
	}

	public function onExit() {
		return '\n';
	}

	public function doRecursion() {
		return true;
	}

	public function getPattern() {
		return new Pattern($this->getName(), Pattern::TYPE_LINE, '( *\$\$ | *\$\$)');
	}
	
	public function getAllowedModes() {
		return array("#DOCUMENT", "tablecell", "listitem", "multiline", "tablecell", "tableheader", "wptableheader", "wptablecell",
				"bordererror", "borderinfo", "borderwarning", "bordersuccess", "bordervalidation", "border",
				"align", "justify", "alignintable", "indent", "left", "right");
	}
}

?>