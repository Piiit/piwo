<?php

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'pwTools/parser/LexerRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRule.php';
require_once INC_PATH.'pwTools/parser/Pattern.php';

class Variable extends ParserRule implements ParserRuleHandler, LexerRuleHandler {
	
	public static $variables = array();
	
	public function getName() {
		return strtolower(__CLASS__);
	}
	
	public function onEntry() {
		$nodeData = $this->getNode()->getData();
		$varname = utf8_strtolower($nodeData[0]);
  		$value = $this->getText();

  		if ($_SESSION['pw_wiki']['error']) {
    		$_SESSION['pw_wiki']['error'] = false;
    		return $value.nop("Die Variable '$varname' kann wegen interner Fehler nicht gesetzt werden.");
  		}

  		self::$variables[$varname] = $value;
	}

	public function onExit() {
		return '';
	}

	public function doRecursion() {
		return false;
	}

	public function getPattern() {
		return new Pattern($this->getName(), Pattern::TYPE_LINE, '!! ([\w]+) *= *');
	}
	
	public function getAllowedModes() {
		return array("#DOCUMENT", "tablecell", "listitem", "multiline", "bordererror", "borderinfo", "borderwarning", 
				"bordersuccess", "bordervalidation", "border", "bold", "underline", "italic", "monospace", "small", "big", 
				"strike", "sub", "sup", "hi", "lo", "em", "tablecell", "tableheader", "wptableheader", "wptablecell",
				"align", "justify", "alignintable", "indent", "left", "right", "pluginparameter");
	}
}

?>