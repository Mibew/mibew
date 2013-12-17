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

/**
 * Renders a view for an operator page
 *
 * At the moment all views are stored in /views folder.
 *
 * $view_name param should include neither full view's path nor its extension.
 * Just view name. For example, to render and output
 * 'styles/operator_pages/default/agents.php' view one should use 'agents' as
 * the view name.
 *
 * @param string $view_name Name of the view to render
 */
function render_view($view_name) {
	// Code of this function replaces code from the global scope. Thus we need
	// to import some variables to make them visible to required views.
	global $page, $mibewroot, $version, $errors;

	// Prepare to output html
	start_html_output();

	// Build full view name. Remove '\' and '/' characters form the specified
	// view name
	$full_view_name = dirname(dirname(__FILE__)) .
		'/styles/operator_pages/default/views/' .
		str_replace("/\\", '', $view_name) . '.php';

	// Load and execute the view
	require($full_view_name);
}

?>