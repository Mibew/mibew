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
 * Represents a style for invitations
 */
class InvitationStyle extends Style implements StyleInterface {
	/**
	 * Builds base path for style files. This path is relative Mibew root and
	 * does not contain neither leading nor trailing slash.
	 *
	 * @return string Base path for style files
	 */
	public function filesPath() {
		return 'styles/invitations/' . $this->name();
	}

	/**
	 * Loads configurations of the style.
	 *
	 * @return array Style configurations
	 */
	public function configurations() {
		return array();
	}

	/**
	 * Stub for StyleInterface::render method.
	 *
	 * The method does not contain actual code because inviation styles are not
	 * renderable now.
	 */
	public function render($template_name) {
		return FALSE;
	}

	/**
	 * Returns name of the style which is currently used in the system
	 *
	 * @return string Name of a style
	 */
	public static function currentStyle() {
		// Load value from system settings
		return Settings::get('invitationstyle');
	}

	/**
	 * Sets style which is currently used in the system
	 *
	 * @param string $style_name Name of a style
	 */
	public static function setCurrentStyle($style_name) {
		Settings::set('invitationstyle', $style_name);
		Settings::update();
	}

	/**
	 * Returns an array which contains names of available styles.
	 *
	 * @param array List of styles names
	 */
	public static function availableStyles() {
		$styles_root = dirname(dirname(dirname(__FILE__))) .
			'/styles/invitations';

		return self::getStyleList($styles_root);
	}

	/**
	 * Returns array of default configurations for concrete style object. This
	 * method uses "Template method" design pattern.
	 *
	 * @return array Default configurations of the style
	 */
	protected function defaultConfigurations() {
		return array();
	}
}

?>