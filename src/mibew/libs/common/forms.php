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

function form_value($key)
{
	global $page;
	if (isset($page) && isset($page["form$key"]))
		return htmlspecialchars($page["form$key"]);
	return "";
}

function form_value_cb($key)
{
	global $page;
	if (isset($page) && isset($page["form$key"]))
		return $page["form$key"] === true;
	return false;
}

function form_value_mb($key, $id)
{
	global $page;
	if (isset($page) && isset($page["form$key"]) && is_array($page["form$key"])) {
		return in_array($id, $page["form$key"]);
	}
	return false;
}

?>