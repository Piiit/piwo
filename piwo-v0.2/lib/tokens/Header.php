<?php

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'pwTools/parser/LexerRuleHandler.php';
require_once INC_PATH.'pwTools/parser/LexerRuleHandlerActive.php';
require_once INC_PATH.'pwTools/parser/ParserRuleHandler.php';
require_once INC_PATH.'pwTools/parser/ParserRule.php';
require_once INC_PATH.'pwTools/parser/Pattern.php';

class Header extends ParserRule implements ParserRuleHandler, LexerRuleHandler, LexerRuleHandlerActive {
	
	private $headerIndex = 0;
	private $level;
	private $indexTable = null;
	
	public function getName() {
		return strtolower(__CLASS__);
	}
	
	public function getPattern() {
		return new Pattern($this->getName(), Pattern::TYPE_LINE, ' *(={1,5})', '={1,5}[\s\t]*');
	}

	public function getAllowedModes() {
		return array(
				"#DOCUMENT", "left", "right", "notoc", "bordererror", "borderinfo", 
				"borderwarning", "bordersuccess", "bordervalidation", "border");
	}
	
	public function onEntry() {

		$node = $this->getNode();
		$nodeData = $node->getData();
		$this->level = strlen($nodeData[0]);
		
		$htxt = trim($this->getText($node));
		if (strlen($htxt) == 0) {
			$htxt = nop("Empty headers are not allowed!");
		}
		
		if ($node->isInside("notoc")) {
			$o = '<h'.$this->level.'>';
		} else {
			$config = $node->getData();
			$level = utf8_strlen($config[0]);
				
			$o = '<h'.$this->level.' id="header_'.$this->headerIndex.'">';
			$this->headerIndex++;
		}

		
 		$o .= $htxt;
		return $o;
	}

	public function onExit() {
		return '</h'.$this->level.'>';
	}

	public function doRecursion() {
		return false;
	}
	
	public function onNewNodeOnEntry() {
		
		$node = $this->getNode();
		$nodeData = $node->getData();
		$level = strlen($nodeData[0]);
		$htxt = trim($this->getText($node));
		$this->indexTable->add($level, $htxt);
		
		TestingTools::debug($this->indexTable);
	}

	public function onNewNodeOnExit() {
	}


}

?>
