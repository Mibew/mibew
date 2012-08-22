<?php

/**
 * Test interaction type for the Mibew API
 */
class MibewAPITestInteraction extends MibewAPIInteraction {

	protected $obligatoryArguments = array(
		'*' => array(
			'return' => array(),
			'references' => array()
		),
		'foo' => array(
			'bar' => 127
		)
	);

	public $reservedFunctionNames = array(
		'result'
	);

}

?>
