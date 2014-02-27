/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
