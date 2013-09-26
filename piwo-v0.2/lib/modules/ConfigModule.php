<?php

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'piwo-v0.2/lib/modules/ModuleHandler.php';
require_once INC_PATH.'piwo-v0.2/lib/modules/Module.php';
require_once INC_PATH.'pwTools/gui/GuiTools.php';
require_once INC_PATH.'pwTools/data/ArrayTools.php';


class ConfigModule extends Module implements ModuleHandler {
	
	public function __construct() {
		parent::__construct($this->getName(), $this);
	}
	
	public function getName() {
		return "config";
	}

	public function getVersion() {
		return "20130905";
	}

	//TODO Change this granularity and make a permissions Class to handle this
	public function permissionGranted($userData) {
		return $userData['group'] == 'admin';
	}
	
	public function execute() {
		
		if (isset($_POST["cancel"])) {
			return;	
		}
		
		$mode = pw_wiki_getmode();
		$id = pw_wiki_getid();
		
		if (isset($_POST['clearsession'])) {
			pw_wiki_unsetcfg();
			pw_wiki_loadconfig();
			unset($_POST['clearsession']);
			$this->setNotification("Session cleared!");
			return;
		}
		
		if (isset($_POST["config"])) {
			pw_wiki_setcfg('debug', ArrayTools::getIfExistsNotNull(false, $_POST, 'debug'));
			if (pw_wiki_getcfg('debug') == false) {
				TestingTools::debugOff();
			}
			pw_wiki_setcfg('useCache', ArrayTools::getIfExistsNotNull(false, $_POST, 'useCache'));
			$this->setNotification("Changes saved!");
			return;
		}
		
		$entries = GuiTools::checkbox("Debug-Modus", "debug", pw_wiki_getcfg('debug'));
		$entries .= GuiTools::checkbox("Use cache", "useCache", pw_wiki_getcfg('useCache'));
		$entries .= GuiTools::button("Clear Session", "clearsession"); 
		$this->setDialog(GuiTools::dialogQuestion($this->getMenuText(), $entries, "config", "OK", "cancel", "Cancel", "id=".$id->getID()."&mode=$mode"));
	}
	
	public function getMenuText() {
		return "Configuration";
	}

	public function getMenuAvailability($mode) {
		return true; //For all modes available
	}


}

?>