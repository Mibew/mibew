<?php

/*
 * You can set this to a different value.
 * See http://www.php.net/manual/en/language.types.callable.php
 */
$password_policy = 'standard_password_policy';

function standard_password_policy ($pwd) {
	if (strlen($pwd) < 8) {
		return false;
	}
	if (strlen($pwd) >= 16) {
		return true;
	}
	
	$character_classes = 0;
	if (preg_match('/[A-Z]/', $pwd)) $character_classes++;
	if (preg_match('/[a-z]/', $pwd)) $character_classes++;
	if (preg_match('/[0-9]/', $pwd)) $character_classes++;
	if (preg_match('/[^A-Za-z0-9]/', $pwd)) $character_classes++;
	
	if ($character_classes >= 3) {
		return true;
	}
	return false;
}

?>
