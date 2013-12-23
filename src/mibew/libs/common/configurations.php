<?php
/*
 * Copyright 2005-2013 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Read and parse configuration ini file
 *
 * @param string $file Path to Configuration file
 * @return boolean|array Array of configurations or boolean false if file can
 * not be read.
 */
function read_config_file($file) {
	if (! is_readable($file)) {
		return false;
	}
	return parse_ini_file($file, true);
}

?>