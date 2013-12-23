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

require_once(dirname(__FILE__).'/common/response.php');
require_once(dirname(__FILE__).'/common/request.php');


/**
 * Renders a view for an operator page
 *
 * All views are stored in "styles/pages/<style_name>" folders.
 *
 * $view_name param should include neither full view's path nor its extension.
 * Just view name. For example, to render and output
 * "styles/pages/default/agents.php" view one should use "agents" as
 * the view name.
 *
 * @param string $view_name Name of the view to render.
 * @param string $style_name Name of the style from which a view should
 *   be rendered. If this param is empty the value from configurations will
 *   be used.
 */
function render_view($view_name, $style_name = NULL) {
	// Code of this function replaces code from the global scope. Thus we need
	// to import some variables to make them visible to required views.
	global $page, $mibewroot, $version, $errors;

	if (empty($style_name)) {
		if (installation_in_progress()) {
			// We currently instal Mibew. Thus we cannot use Database and
			// Settings classes. Just use "default" style for installation pages.
			$style_name = 'default';
		} else {
			$style_name = get_page_style();
		}
	}

	// Prepare to output html
	start_html_output();

	// Build full view name. Remove '\' and '/' characters form the specified
	// view name
	$full_view_name = dirname(dirname(__FILE__)) .
		'/styles/pages/' . $style_name . '/views/' .
		str_replace("/\\", '', $view_name) . '.php';

	// Load and execute the view
	require($full_view_name);
}

?>