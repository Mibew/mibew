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

var popupStatus = 0;

function loadPopup(){
	if(popupStatus==0){
		$("#background-popup").css({
			"opacity": "0.7"
		});
		$("#background-popup").fadeIn("slow");
		$("#dashboard-locales-popup").fadeIn("slow");
		popupStatus = 1;
	}
}
function disablePopup(){
	if(popupStatus==1){
		$("#background-popup").fadeOut("slow");
		$("#dashboard-locales-popup").fadeOut("slow");
		popupStatus = 0;
	}
}

function normpos(a) {
	if(a < 10) {
		return 10;
	}
	return a;
}

function centerPopup(){
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#dashboard-locales-popup").height();
	var popupWidth = $("#dashboard-locales-popup").width();
	$("#dashboard-locales-popup").css({
		"position": "absolute",
		"top": normpos((windowHeight-popupHeight) * 0.2),
		"left": normpos(windowWidth/2-popupWidth/2)
	});
	$("#background-popup").css({
		"height": windowHeight
	});
}

$(function(){
	$("#change-language").click(function(){
		centerPopup();
		loadPopup();
		return false;
	});
	$("#dashboard-locales-popup-close").click(function(){
		disablePopup();
		return false;
	});
	$("#background-popup").click(function(){
		disablePopup();
	});
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});
});
