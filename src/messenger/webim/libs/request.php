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

function build_js_response($response)
{
	$result = array('load' => (isset($response['load'])?$response['load']:array()),
			'handlers' => (isset($response['handlers'])?$response['handlers']:array()),
			'dependences' => (isset($response['dependences'])?$response['dependences']:array()),
			'data' => (isset($response['data'])?$response['data']:array()));
	return "mibewOnResponse(" . json_encode($result) . ");";
}

?>