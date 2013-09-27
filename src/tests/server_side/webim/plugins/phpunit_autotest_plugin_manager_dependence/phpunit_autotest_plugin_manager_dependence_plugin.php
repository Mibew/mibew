<?php

/**
 * Test plugin for PHPUnit tests
 */
Class PhpunitAutotestPluginManagerDependencePlugin extends Plugin{

	public $listenersRegistered = false;

	public function getWeight() {
		return 10;
	}

	public function registerListeners() {}

	public static function getDependences() {
		return array('some_missed_dependence');
	}

	public function __construct(){
		$this->initialized = true;
	}

}

?>