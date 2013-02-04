<?php

/**
 * Test plugin for PHPUnit tests
 */
Class RequestProcessorTestPlugin extends Plugin{

	public $callList = array();

	public $errorCode = 0;

	public $callbackArguments = array();

	public function __construct() {
		$this->initialized = true;
	}

	public function getWeight() {
		return 10;
	}

	public function registerListeners() {
		$processor_events = array(
			'testRequestReceived',
			'testRequestError',
			'testResponseReceived',
			'testCallError',
			'testFunctionCall',
			'testRequestProcessorCallback'
		);
		$dispatcher = EventDispatcher::getInstance();
		foreach ($processor_events as $event) {
			$dispatcher->attachListener($event, $this, $event);
		}
	}

	public function testRequestReceived(&$arguments) {
		array_push($this->callList, 'testRequestReceived');
	}

	public function testRequestError(&$arguments) {
		array_push($this->callList, 'testRequestError');
		$this->errorCode = $arguments['exception']->getCode();
	}

	public function testResponseReceived(&$arguments) {
		array_push($this->callList, 'testResponseReceived');
	}

	public function testCallError(&$arguments) {
		array_push($this->callList, 'testCallError');
		$this->errorCode = $arguments['exception']->getCode();
	}

	public function testFunctionCall(&$arguments) {
		array_push($this->callList, 'testFunctionCall');
		if ($arguments['function'] == 'call_func') {
			$arguments['results']['pluginCall'] = true;
		}
	}

	public function testRequestProcessorCallback(&$arguments) {
		array_push($this->callList, 'testRequestProcessorCallback');
		$this->callbackArguments = $arguments;
	}
}

?>