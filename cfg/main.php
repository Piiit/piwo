<?php

  const PIWOVERSION = "0.1.9 - Alpha";
  const WIKIADMINUSER = "root";
  const WIKIADMINPASSWORD = "qwertz"; //TODO Security issue... Encryption needed!!!
  const WIKISTORAGE = "dat";
  const WIKICACHE = "home";
  const WIKIFILEEXT = ".txt";
  const WIKICACHEFILEEXT = ".html";
  const WIKINSDEFAULTPAGE = "_index";
  const WIKITEMPLATE = "tpl";	//TODO should be outside STORAGE
  const WIKITEMPLATESNS = ":tpl:";	//TODO no ns for templates, should be outside STORAGE
  const WIKIDESCRIPTION = "coding, brainstorming, testing, hoffen und suden";
  const WIKIKEYWORDS = "php, javascript, java, cms, wiki, tests";
  const NOTIFICATION_DELAY_MINIMUM = 1500; //in ms
  const NOTIFICATION_DELAY_PER_LETTER = 30;
  const DEFAULT_LANGUAGE = "en";
  
  $WIKIDEFAULTCONFIG = array ( 
			"debug"    	=> false,
 		    "useCache"	=> false,
    		"login"	 	=> array('group' => 'users', 'user' => 'guest'),
            "wikititle" => "piwo",
            "titledesc" => PIWOVERSION
            );

?>