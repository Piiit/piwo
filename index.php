<?php/* * PHP error handling */error_reporting(E_ALL | E_STRICT);ini_set('display_errors', true);ini_set('auto_detect_line_endings', false);/* * Include path config to find functions, classes, configfiles, templates, etc. */define ('INC_PATH', realpath(__DIR__.'/../').'/');define ('PW_WIKI_PATH', INC_PATH.'piwo-v0.2/');define ('PW_TOOLS_PATH', INC_PATH.'pwTools/');$includePaths = array(	PW_WIKI_PATH.'cfg/',	PW_WIKI_PATH.'lib/',	PW_WIKI_PATH.'lib/modules',	PW_TOOLS_PATH);set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, $includePaths));require_once INC_PATH.'pwTools/system/SystemTools.php';SystemTools::autoload($includePaths);/* * Start session and load default config, if not done already... */session_start();if(! WikiTools::isSessionInfoLoaded()) {	WikiTools::loadConfigToSession();}/* * Configure debug and log output */TestingTools::debugOn();TestingTools::logOn();TestingTools::outputOff();// TestingTools::inform($_REQUEST);// TestingTools::inform($_SESSION, "SESSION");//TODO make template filenames and paths configurable...FileTools::createFolderIfNotExist(WikiConfig::WIKISTORAGE);FileTools::createFolderIfNotExist(WikiConfig::WIKISTORAGE."/tpl");FileTools::copyFileIfNotExist(	WikiConfig::CONFIGPATH."/skeleton/tpl/".WikiConfig::DEFAULT_LANGUAGE."/firststart.txt", 	WikiConfig::WIKISTORAGE."/".WikiConfig::WIKINSDEFAULTPAGE.WikiConfig::WIKIFILEEXT);FileTools::copyMultipleFilesIfNotExist(	WikiConfig::CONFIGPATH."/skeleton/tpl/".WikiConfig::DEFAULT_LANGUAGE."/*.txt", 	WikiConfig::WIKISTORAGE."/".WikiConfig::WIKITEMPLATE."/");	/* * Load GUI modules... */new LoginModule();new ConfigModule();new ShowSourceModule();$editModule = new EditModule(); $showPagesModule = new ShowPagesModule();new NewPageModule();new DeletePageModule();new DeleteNamespaceModule();new RenameModule();new MoveModule();$defaultModule = new ShowContentModule();//  TestingTools::inform($_GET);//  TestingTools::inform($_POST); $module = null;$notification = "";$scriptsText = "";$notificationType = null;$mode = WikiTools::getCurrentMode() == null ? $defaultModule->getName() : WikiTools::getCurrentMode();try {	try {		$module = Module::getModuleList()->get($mode);		if($module instanceof JavaScriptProvider) {			$scriptsText .= $module->getJavaScript()."<!-- INSERTED BY MODULE ".$module->getName()." -->\n";		}			} catch (Exception $e) {		throw new Exception("Mode with ID '$mode' does not exist!");	}	if ($module instanceof PermissionProvider && !$module->permissionGranted()) {		throw new Exception("Module '".$module->getName()."': Access denied.");	}		if($module != $defaultModule) {		$module->execute();	}		} catch (Exception $e) {	$notification = $e->getMessage();	$notificationType = "error";}try {		try {
		$body = $module == null ? null : $module->getDialog();
		if($body == null) {
			$defaultModule->execute();
			$body = $defaultModule->getDialog();
		}
	} catch (Exception $e) {		$notificationType = "error";		if($editModule->permissionGranted()) {			$editModule->execute();			$body = $editModule->getDialog();			$notification = "Syntax error<br />".$e->getMessage();		} else {			$showPagesModule->execute();
			$body = $showPagesModule->getDialog();			$notification = "Unable to show:<br />This read-only file contains syntax errors!";		}
	}		$notification = $notification == null ? $module->getNotification() : $notification;	if($notification != null) {				// no notification set.		if($notificationType == null) {			$notificationType = $module->getNotificationType() == Module::NOTIFICATION_INFO ? "info" : "error";		}		$notification = "<div id='notification' class='$notificationType'>$notification</div>";	}		$menu = pw_wiki_getmenu(WikiTools::getCurrentID(), $mode, Module::getModuleList()); 	$notificationDelay = WikiConfig::NOTIFICATION_DELAY_MINIMUM + WikiConfig::NOTIFICATION_DELAY_PER_LETTER * strlen($notification);	/*	 * Reading the main page template, and replacing all placeholders with	 * their corresponding content.	 * 	 * TODO make template filenames and paths configurable...	 */	$mainpage = str_replace(			array(	
				"{{pagetitle}}",				"{{pagedescription}}",				"{{pagekeywords}}",				"{{scripts}}",				"{{notification}}",				"{{notification_delay}}",				"{{wikititle}}",				"{{titledesc}}",				"{{startpage}}",				"{{body}}",				"{{mainmenu}}"
			), 			array(				WikiTools::getHtmlTitle(),				WikiConfig::WIKIDESCRIPTION,				WikiConfig::WIKIKEYWORDS,				rtrim($scriptsText),				$notification,				$notificationDelay,				WikiTools::getSessionInfo('wikititle'),				WikiTools::getSessionInfo('titledesc'),				":".WikiConfig::WIKINSDEFAULTPAGE,				$body,				$menu			), 			file_get_contents(WikiConfig::CONFIGPATH."/skeleton/mainpage.tmpl")		);} catch (Exception $e) {	echo "<pre>".$e->getMessage()."<hr />".$e->getTraceAsString()."</pre>";}$debugpage = "";if(WikiTools::getSessionInfo("debug")) {	//TODO make template filenames and paths configurable...	$debugpage = file_get_contents(CFG_PATH."skeleton/debug.tmpl");	$log = TestingTools::getLog();	$log->setLogLevel(Log::DEBUG);	$debugpage = str_replace("{{debug}}", "Debug-Mode ON... Log-output:<br />".$log->toStringReversed(), $debugpage);}$mainpage = str_replace("{{debugoutput}}", $debugpage, $mainpage);echo $mainpage;// 	case "update":// 		$output = pw_wiki_create_cached_page($id, true);// 	break;// 	case "updatecache":// 		pw_wiki_update_cache();// 		$output = "UPDATING CACHE... DONE!";// 	break;// 	case "updatecacheforced":// 		pw_wiki_update_cache(true);// 		$output = "FORCED UPDATING CACHE... DONE!";// 	break;// }?>