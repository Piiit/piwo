<?php

if (!defined('INC_PATH')) {
	define ('INC_PATH', realpath(dirname(__FILE__).'/../../').'/');
}
require_once INC_PATH.'piwo-v0.2/lib/modules/ModuleHandler.php';
require_once INC_PATH.'piwo-v0.2/lib/modules/Module.php';
require_once INC_PATH.'pwTools/gui/GuiTools.php';

class NewPageModule extends Module implements ModuleHandler {
	
	public function getName() {
		return "newpage";
	}

	public function getVersion() {
		return "20130915";
	}

	public function permissionGranted($userData) {
		return $userData['group'] == 'admin';
	}
	
	public function execute() {
		
		if(isset($_POST["cancel"])) {
			return;
		}
		
		$id = pw_wiki_getid();
		$mode = pw_wiki_getmode();
		
		$idText = pw_s2e($id->getID());
		
		if(isset($_POST["create"])) {
			pw_wiki_setcfg("eventmode", "edit");
			pw_wiki_setcfg("eventid", $id);
			pw_wiki_setcfg("eventfrom", $this->getName());
// 			TestingTools::inform("SETTING");
			return;
		}
		
		$entries = "<p>Namespaces get separated by <tt>:</tt>, e.g. <tt>Manual:Page1</tt><br />If the page already exists, it will be opened for editing.</p>";
		$entries .= GuiTools::textInput("ID", "id", $idText);
		//$entries .= "<input type='hidden' name='mode' value='edit' />";
		//FIXME Jumping to another mode without hidden fields and get, should come into this module ones more and then redirected to the other one...
		$this->setDialog(GuiTools::dialogQuestion("Create a new page", $entries, "create", "OK", "cancel", "Cancel", "mode=$mode&id=".$id->getID()));
		
	}
	
	public function getMenuText() {
		return "New";
	}

	public function getMenuAvailability($mode) {
		return true; 
	}


}

?>