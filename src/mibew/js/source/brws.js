/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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

var myAgent = "";
var myVer = 0;
var myRealAgent = "";

function detectAgent() {
	var AGENTS = ["opera","msie","safari","firefox","netscape","mozilla"];
	var agent = navigator.userAgent.toLowerCase();
	for (var i = 0; i < AGENTS.length; i++) {
		var agentStr = AGENTS[i];
		if (agent.indexOf(agentStr) != -1) {
			myAgent = agentStr;
			if (!window.RegExp)
				break;

			var versionExpr = new RegExp(agentStr + "[ \/]?([0-9]+(\.[0-9]+)?)");
			if (versionExpr.exec(agent) != null) {
				myVer = parseFloat(RegExp.$1);
			}
			break;
		}
	}
	myRealAgent = myAgent;
	if( navigator.product == "Gecko")
		myAgent = "moz";
}
detectAgent();

function getEl(name) {
	return document.getElementById(name);
}
