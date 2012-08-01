<?php

/**
 * Test plugin for PHPUnit tests
 */
Class Phpunit_autotest_plugin_managerPlugin extends Plugin{

	public $eventsRegistered = false;
	public $listenersRegistered = false;

	public function getWeight() {
		return 10;
	}

	public function registerEvents() {
		$this->eventsRegistered = true;
		$this->checkRegistration();
	}

	public function registerListeners() {
		$this->listenersRegistered = true;
		$this->checkRegistration();
	}

	public function checkRegistration() {
		if ($this->eventsRegistered && $this->listenersRegistered) {
			$GLOBALS['phpunit_autotest_plugin_manager'] = true;
		}
	}

	public function testEventListener(&$vars) {
		$vars['test'] = 'some_test_value';
	}

	public function __construct(){
		$this->initialized = true;
	}

}

?>