<?php

function request_processor_callback($arguments) {
	$dispatcher = EventDispatcher::getInstance();
	$dispatcher->triggerEvent('testRequestProcessorCallback', $arguments);
}

?>