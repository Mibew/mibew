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

function loadNews() {
	if (typeof(window.mibewNews) == "undefined" || typeof(window.mibewNews.length) == "undefined") {
		return;
    }

	var str = "<div>";
	for (var i = 0; i < window.mibewNews.length; i++) {
		str += "<div class=\"newstitle\"><a hre" + "f=\"" + window.mibewNews[i].link + "\">" + window.mibewNews[i].title + "</a>, <span class=\"small\">" + window.mibewNews[i].date + "</span></div>";
		str += "<div class=\"newstext\">" + window.mibewNews[i].message+"</div>";
	}
	$("#news").html(str + "</div>");
}

function loadVersion() {
	if(typeof(window.mibewLatest) == "undefined" || typeof(window.mibewLatest.version) == "undefined") {
		return;
    }

	var current = $("#cver").html();

	if(current != window.mibewLatest.version) {
		if(current < window.mibewLatest.version) {
			$("#cver").css("color","red");
		}
		$("#lver").html(window.mibewLatest.version+", Download <a href=\""+window.mibewLatest.download+"\">"+window.mibewLatest.title+"</a>");
	} else {
		$("#cver").css("color","green");
		$("#lver").html(window.mibewLatest.version);
	}
}

$(function(){
	loadNews();
	loadVersion();
});