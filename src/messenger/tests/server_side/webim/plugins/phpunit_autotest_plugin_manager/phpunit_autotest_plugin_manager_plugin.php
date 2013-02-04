<?php

/**
 * Test plugin for PHPUnit tests
 */
Class PhpunitAutotestPluginManagerPlugin extends Plugin{

	public $listenersRegistered = false;

	public function getWeight() {
		return 10;
	}

	public function registerListeners() {
		$this->listenersRegistered = true;
		$GLOBALS['phpunit_autotest_plugin_manager'] = true;
	}

	public function testEventListener(&$vars) {
		$vars['test'] = 'some_test_value';
	}

	public function __construct(){
		$this->initialized = true;
	}

}

?>