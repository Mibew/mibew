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
 * Represents a style for operator pages
 */
class PageStyle extends Style implements StyleInterface {
	/**
	 * Builds base path for style files. This path is relative Mibew root and
	 * does not contain neither leading nor trailing slash.
	 *
	 * @return string Base path for style files
	 */
	public function filesPath() {
		return 'styles/pages/' . $this->name();
	}

	/**
	 * Renders template file to HTML and send it to the output
	 *
	 * @param string $template_name Name of the template file without path but
	 * with extension
	 */
	public function render($template_name) {
		// We need to import some variables to make them visible to required
		// view.
		global $page, $mibewroot, $version, $errors;

		// Prepare to output html
		start_html_output();

		// Build full view name. Remove '\' and '/' characters form the
		// specified view name
		$full_view_name = MIBEW_FS_ROOT . '/' . $this->filesPath() . '/views/' .
			str_replace("/\\", '', $template_name) . '.php';

		// Load and execute the view
		require($full_view_name);
	}

	/**
	 * Returns name of the style which shoud be used for the current request.
	 *
	 * Result of the method can depends on user role, requested page or any
	 * other criteria.
	 *
	 * @return string Name of a style
	 */
	public static function currentStyle() {
		// Just use the default style
		return self::defaultStyle();
	}

	/**
	 * Returns name of the style which is used in the system by default.
	 *
	 * @return string Name of a style
	 */
	public static function defaultStyle() {
		// Load value from system settings
		return Settings::get('page_style');
	}

	/**
	 * Sets style which is used in the system by default
	 *
	 * @param string $style_name Name of a style
	 */
	public static function setDefaultStyle($style_name) {
		Settings::set('page_style', $style_name);
		Settings::update();
	}

	/**
	 * Returns an array which contains names of available styles.
	 *
	 * @param array List of styles names
	 */
	public static function availableStyles() {
		$styles_root = MIBEW_FS_ROOT . '/styles/pages';

		return self::getStyleList($styles_root);
	}

	/**
	 * Returns array of default configurations for concrete style object. This
	 * method uses "Template method" design pattern.
	 *
	 * @return array Default configurations of the style
	 */
	protected function defaultConfigurations() {
		return array(
			'chat' => array(
				'window_params' => ''
			),
			'mail' => array(
				'window_params' => ''
			),
			'screenshots' => array()
		);
	}
}

?>