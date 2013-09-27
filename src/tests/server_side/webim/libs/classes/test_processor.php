<?php

class TestProcessor extends RequestProcessor {

	public $callList = array();

	public $responses = array();

	public function __construct($config = array()) {
		$config += array(
			'event_prefix' => 'test'
		);
		parent::__construct($config);
	}

	protected function getMibewAPIInstance() {
		return MibewAPI::getAPI('MibewAPITestInteraction');
	}

	protected function processRequest($request, $result_function = null) {
		array_push($this->callList, 'processRequest');
		return parent::processRequest($request, $result_function);
	}

	protected function processFunction($function, MibewAPIExecutionContext &$context) {
		array_push($this->callList, 'processFunction');
		return parent::processFunction($function, $context);
	}

	/**
	 * @todo Think about callbacks saving
	 */
	protected function saveCallback($token, $callback) {
		array_push($this->callList, 'saveCallback');
	}

	protected function loadCallback($token) {
		array_push($this->callList, 'loadCallback');
		if ($token == 'callback_only_test') {
			return array('function' => 'time', 'arguments' => array());
		} elseif($token == 'callback_and_result_test') {
			return array('function' => 'request_processor_callback', 'arguments' => array('second' => true));
		}
		return null;
	}

	protected function sendSyncRequest($request) {
		array_push($this->callList, 'sendSyncRequest');
		$return_result = false;
		foreach ($request['functions'] as $function) {
			if ($function['function'] == 'return_result') {
				$return_result = true;
				break;
			}
		}
		if ($return_result) {
			return array(
				'requests' => array(
					array(
						'token' => $request['token'],
						'functions' => array(
							array(
								'function' => 'result',
								'arguments' => array(
									'some_argument' => 'some_value',
									'return' => array(),
									'references' => array()
								)
							)
						)
					)
				)
			);
		}
		return array(
			'requests' => array(
				array(
					'token' => $request['token'],
					'functions' => array(
						array(
							'function' => 'not_a_result',
							'arguments' => array(
								'some_argument' => 'some_value',
								'return' => array(),
								'references' => array()
							)
						)
					)
				)
			)
		);
	}

	protected function sendAsyncRequest($request) {
		array_push($this->callList, 'sendAsyncRequest');
	}

	protected function sendSyncResponses($responses) {
		array_push($this->callList, 'sendSyncResponses');
		$this->responses = $responses;
	}

	protected function sendAsyncResponses($responses) {
		array_push($this->callList, 'sendAsyncResponses');
	}

	protected function processorCall(&$func) {
		array_push($this->callList, 'processorCall');
		if ($func['function'] == 'call_func') {
			$func['results']['processorCall'] = true;
		}
	}
}

?>
