<?phperror_reporting(E_ALL | E_STRICT);ini_set('display_errors', True);if (!defined('INC_PATH')) {	define ('INC_PATH', realpath(dirname(__FILE__).'/../').'/');}if (!defined('MODULE_PATH')) {	define ('MODULE_PATH', INC_PATH.'piwo-v0.2/lib/modules/');}if (!defined('PLUGIN_PATH')) {	define ('PLUGIN_PATH', INC_PATH.'piwo-v0.2/plugins/');}if (!defined('CFG_PATH')) {	define ('CFG_PATH', INC_PATH.'piwo-v0.2/cfg/');}require_once INC_PATH.'piwo-v0.2/lib/common.php';require_once INC_PATH.'piwo-v0.2/lib/lexerconf.php';require_once INC_PATH.'piwo-v0.2/lib/admin.php';require_once CFG_PATH.'main.php';require_once INC_PATH.'pwTools/system/SystemTools.php';SystemTools::autoloadInit(	array(		PLUGIN_PATH, 		MODULE_PATH, 		INC_PATH."pwTools",		INC_PATH."piwo-v0.2/lib"	));session_start();pw_wiki_loadconfig();TestingTools::debugOn();TestingTools::logOn();TestingTools::outputOff();// TestingTools::inform($_REQUEST);TestingTools::inform($_SESSION, "SESSION");FileTools::createFolderIfNotExist(WIKISTORAGE);FileTools::createFolderIfNotExist(WIKISTORAGE."/tpl");FileTools::copyFileIfNotExist("cfg/skeleton/tpl/".DEFAULT_LANGUAGE."/firststart.txt", WIKISTORAGE."/".WIKINSDEFAULTPAGE.WIKIFILEEXT);FileTools::copyMultipleFilesIfNotExist("cfg/skeleton/tpl/".DEFAULT_LANGUAGE."/*.txt", WIKISTORAGE."/".WIKITEMPLATE."/");	new LoginModule();new ConfigModule();new ShowSourceModule();$editModule = new EditModule(); $showPagesModule = new ShowPagesModule();new NewPageModule();new DeletePageModule();new DeleteNamespaceModule();new RenameModule();new MoveModule();$defaultModule = new ShowContentModule();//  TestingTools::inform($_GET);//  TestingTools::inform($_POST); $module = null;$notification = "";$scriptsText = "";$notificationType = null;$mode = pw_wiki_getmode() == null ? $defaultModule->getName() : pw_wiki_getmode();try {	try {		$module = Module::getModuleList()->get($mode);		if($module instanceof JavaScriptProvider) {			$scriptsText .= $module->getJavaScript()."<!-- INSERTED BY MODULE ".$module->getName()." -->\n";		}			} catch (Exception $e) {		throw new Exception("Mode with ID '$mode' does not exist!");	}	if ($module instanceof PermissionProvider && !$module->permissionGranted()) {		throw new Exception("Module '".$module->getName()."': Access denied.");	}		if($module != $defaultModule) {		$module->execute();	}		} catch (Exception $e) {	$notification = $e->getMessage();	$notificationType = "error";}try {		try {
		$body = $module == null ? null : $module->getDialog();
		if($body == null) {
			$defaultModule->execute();
			$body = $defaultModule->getDialog();
		}
	} catch (Exception $e) {		$notificationType = "error";		if($editModule->permissionGranted()) {			$editModule->execute();			$body = $editModule->getDialog();			$notification = "Syntax error<br />".$e->getMessage();		} else {			$showPagesModule->execute();
			$body = $showPagesModule->getDialog();			$notification = "Unable to show:<br />This read-only file contains syntax errors!";		}
	}		$notification = $notification == null ? $module->getNotification() : $notification;	if($notification != null) {				// no notification set.		if($notificationType == null) {			$notificationType = $module->getNotificationType() == Module::NOTIFICATION_INFO ? "info" : "error";		}		$notification = "<div id='notification' class='$notificationType'>$notification</div>";	}		$menu = pw_wiki_getmenu(pw_wiki_getid(), $mode, Module::getModuleList()); 	$notificationDelay = NOTIFICATION_DELAY_MINIMUM + NOTIFICATION_DELAY_PER_LETTER * strlen($notification);	$mainpage = file_get_contents(CFG_PATH."skeleton/mainpage.html");	$mainpage = str_replace("{{pagetitle}}", pw_wiki_getfulltitle(), $mainpage);	$mainpage = str_replace("{{pagedescription}}", WIKIDESCRIPTION, $mainpage);	$mainpage = str_replace("{{pagekeywords}}", WIKIKEYWORDS, $mainpage);	$mainpage = str_replace("{{scripts}}", rtrim($scriptsText), $mainpage);	$mainpage = str_replace("{{notification}}", $notification, $mainpage);	$mainpage = str_replace("{{notification_delay}}", $notificationDelay, $mainpage);	$mainpage = str_replace("{{wikititle}}", pw_wiki_getcfg('wikititle'), $mainpage);	$mainpage = str_replace("{{titledesc}}", pw_wiki_getcfg('titledesc'), $mainpage);	$mainpage = str_replace("{{startpage}}", ":".WIKINSDEFAULTPAGE, $mainpage);	$mainpage = str_replace("{{body}}", $body, $mainpage);	$mainpage = str_replace("{{mainmenu}}", $menu, $mainpage);	} catch (Exception $e) {	echo "<pre>".$e->getMessage()."<hr />".$e->getTraceAsString()."</pre>";}$debugpage = "";if(pw_wiki_getcfg("debug")) {	$debugpage = file_get_contents(CFG_PATH."skeleton/debug.html");	$log = TestingTools::getLog();	$log->setLogLevel(Log::DEBUG);	$debugpage = str_replace("{{debug}}", "Debug-Mode ON... Log-output:<br />".$log->toStringReversed(), $debugpage);}$mainpage = str_replace("{{debugoutput}}", $debugpage, $mainpage);echo $mainpage;// 	case "update":// 		$output = pw_wiki_create_cached_page($id, true);// 	break;// 	case "updatecache":// 		pw_wiki_update_cache();// 		$output = "UPDATING CACHE... DONE!";// 	break;// 	case "updatecacheforced":// 		pw_wiki_update_cache(true);// 		$output = "FORCED UPDATING CACHE... DONE!";// 	break;// }?>