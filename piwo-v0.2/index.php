<?php
  	define ('PLUGIN_PATH', realpath(dirname(__FILE__)).'/plugins/');
  }
  if (!defined('CFG_PATH')) {
  	define ('INC_PATH', '../');
  }
  	foreach ($files as $file) {
		copy($file, pw_wiki_getcfg("storage")."/tpl/".basename($file));
  }
  