<?php

require_once dirname(__FILE__) . '/../../../../../mibew/libs/classes/mibew_api_interaction.php';
require_once dirname(__FILE__) . '/mibew_api_test_interaction.php';

/**
 * Test class for MibewAPIInteraction.
 */
class MibewAPIInteractionTest extends PHPUnit_Framework_TestCase {

	/**
	 * An instance of the MibewAPITestInteraction
	 * @var MibewAPITestInteraction
	 */
	protected $object = null;

	protected function setUp() {
		$this->object = new MibewAPITestInteraction();
	}

	protected function tearDown() {
		unset($this->object);
	}

	public function testGetObligatoryArguments() {
		// Test obligatory arguments for all functions
		$this->assertEquals(
			$this->object->getObligatoryArguments('some_default_function'),
			array('return', 'references')
		);

		// Test obligatory argumens for specific function
		$this->assertEquals(
			$this->object->getObligatoryArguments('foo'),
			array('return', 'references', 'bar')
		);
	}

	public function testGetObligatoryArgumentsDefaults() {
		// Test default values for obligatory arguments for all functions
		$this->assertEquals(
			$this->object->getObligatoryArgumentsDefaults('some_default_function'),
			array(
				'return' => array(),
				'references' => array()
			)
		);

		// Test default values for obligatory argumens for specific function
		$this->assertEquals(
			$this->object->getObligatoryArgumentsDefaults('foo'),
			array(
				'return' => array(),
				'references' => array(),
				'bar' => 127
			)
		);
	}
}

?>
