<?php
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'piwo-v0.2/lib/common.php';
require_once INC_PATH.'piwo-v0.2/cfg/main.php';

	$wikiParser = new WikiParser($pathToTokens, $pathToPlugins);
		TestingTools::debug ( "PATTERN TABLE: \n" . $wikiParser->getLexer ()->getPatternTableAsString () );
		$treePrinter = new TreeWalker ( $wikiParser->getLexer ()->getRootNode (), new TreePrinter () );
		TestingTools::inform ( "PARSE TREE: \n" . StringTools::showLineNumbers ( $treePrinter->getResult () ) );
		TestingTools::inform ( "SPEED: Text parsed in " . $wikiParser->getLexer()->getExecutionTime()." seconds!" );
		TestingTools::inform ( "SOURCE:\n" . StringTools::showLineNumbers ( $wikiParser->getSource () ) );
		//$debugString .= "<h3>Debug: Parser - Schritte (TODO: ADAPT TO NEW LEXER)</h3>";
		//$lexer->printDebugInfo(1,1);
	
	}
}
	$lexer->addWordPattern("newline", '(?<=\n)\n');
	$lexer->addSectionPattern("wptable", '\n\{\|', '\|\}');
	$lexer->addSectionPattern("wptableline", '\|-', '(?=\|\}|\|-)');
	#$lexer->addSectionPattern("wptableheader", '\!\! *|\n\! *', '(?= *\!\!|\n)');
	$lexer->addSectionPattern("wptableheader", '(\!\! *|\n\! *)', '(?= *\!\!|\n)'); //ENTRY must be bracketed... otherwise wptable finds its EXIT too early... wrong count!
	$lexer->addSectionPattern("wptablecell", '(\|\| *|\n\|(?![\}-]) *)', '(?= *\|\||\n)');
	$lexer->addLinePattern("wptabletitle", '\|\+ *');
	$lexer->addWordPattern("wptableconfig", '([\w]+) *= *[\"\']?([^\"\']*)[\"\']? *\|* *');
	$lexer->connectTo("wptableheader", "wptableline2");
	$lexer->connectTo("wptablecell", "wptableline2");
	
	// TODO: Aggregate categories and cleanup, Attention with Deflists and Modi, which must (not) be a part of their selfs...
	$blocks = array("#DOCUMENT", "tablecell", "listitem", "multiline");
	$boxes = array("bordererror", "borderinfo", "borderwarning", "bordersuccess", "bordervalidation", "border");
	$lexer->setAllowedModes("newline", array_merge($blocks, $boxes, array("#DOCUMENT", "multiline")));
	$lexer->setAllowedModes("wptable", array("#DOCUMENT", "multiline", "wptablecell"));
	$lexer->setAllowedModes("wptableline", array("wptable"));
	$lexer->setAllowedModes("wptabletitle", array("wptable"));
	$lexer->setAllowedModes("wptableheader", array("wptableline", "wptable"));
	$lexer->setAllowedModes("wptablecell", array("wptableline", "wptable"));
	$lexer->setAllowedModes("wptableconfig", array("wptable", "wptableline", "wptableheader", "wptablecell", "wptabletitle"));

	return $lexer;
}