<?php
	define ('INC_PATH', '../');
}
	
	$np = new TreeWalker($root, new TreePrinter());
	
	$o = "";
	$o .= "<pre style='white-space: pre-wrap'>";
	$o .= "\n\nSOURCE: \n".TextFormat::showLineNumbers(pw_s2e($lexer->getSource()));
	//$o .= "\n\nPARSER STEP-BY-STEP: \n".$lexer->printDebugInfo(1,1, false);
	#$o .= "\n\nOUTPUT: \n";
	$o .= "</pre>";
	echo $o;
	$o .= "\n\nPATTERNTABLE: \n";
	$o .= $lexer->getPatternTableAsString();
