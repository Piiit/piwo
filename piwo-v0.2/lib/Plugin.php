<?php
abstract class Plugin {
	
	private $_data = array();
	
	abstract public function runBefore();
	abstract public function runAfter();
	
	public function setData($id, $data) {
		
	}
	
	public function getData() {
		
	}
	
	public function registerMethod($id, $name) {
		
	}
	
	public function callMethod($id) {
		
	}
}

?>