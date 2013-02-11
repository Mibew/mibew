/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var mibewRequestedScripts = new Array();
var mibewHandlers = new Array();
var mibewHandlersDependences = new Array();

function mibewMakeRequest()
{
	// Try to get user id from local cookie
	var userId = mibewReadCookie(mibewVisitorCookieName);

	mibewDoLoadScript(
		mibewRequestUrl + '&rnd=' + Math.random()
			+ ((userId !== false) ? '&user_id=' + userId : ''),
		'responseScript'
	);
}

function mibewOnResponse(response)
{
	var load = response.load;
	var handlers = response.handlers;
	var data = response.data;
	var dependences = response.dependences;

	for(id in load){
		if(! (load[id] in mibewRequestedScripts)){
			mibewRequestedScripts[id] = new Array();
			mibewRequestedScripts[id]['url'] = load[id];
			mibewRequestedScripts[id]['status'] = 'loading';
			mibewLoadScript(id);
		}
	}

	for(handler in dependences){
		if(! (handler in mibewHandlersDependences)){
			mibewHandlersDependences[handler] = dependences[handler];
		}
	}

	for(var i = 0; i < handlers.length; i++){
		var handlerName = handlers[i];
		if(mibewCanRunHandler(handlers[i])){
			window[handlerName](data);
		}else{
			if(! (handlers[i] in mibewHandlers)){
				mibewHandlers[handlerName] = function(){
					window[handlerName](data);
				};
			}
		}
	}

	mibewCleanUpAfterRequest();

	window.setTimeout(mibewMakeRequest,mibewRequestTimeout);
}

function mibewCleanUpAfterRequest()
{
	document.getElementsByTagName('head')[0].removeChild(document.getElementById('responseScript'));
}

function mibewDoLoadScript(url, id)
{
	var script = document.createElement('script');
	script.setAttribute('type', 'text/javascript');
	script.setAttribute('src', url);
	script.setAttribute('id', id);
	document.getElementsByTagName('head')[0].appendChild(script);
	return script;
}

function mibewLoadScript(id)
{
	var script = mibewDoLoadScript(mibewRequestedScripts[id]['url'], id);
	script.onload = function(){
		mibewScriptReady(id);
	}
	script.onreadystatechange = function(){
		if (this.readyState == 'complete' || this.readyState == 'loaded') {
			mibewScriptReady(id);
		}
	}
}

function mibewScriptReady(id)
{
	mibewRequestedScripts[id]['status'] = 'ready';
	for(handlerName in mibewHandlers){
		if(mibewCanRunHandler(handlerName)){
			mibewHandlers[handlerName]();
			delete mibewHandlers[handlerName];
		}
	}
}

function mibewCanRunHandler(handlerName)
{
	var dependences = mibewHandlersDependences[handlerName];
	for(var i = 0; i < dependences.length; i++){
		if(mibewRequestedScripts[dependences[i]]['status'] != 'ready'){
			return false;
		}
	}
	return true;
}

/**
 * Create session cookie for top level domain with path equals to '/'.
 *
 * @param {String} name Cookie name
 * @param {String} value Cookie value
 */
function mibewCreateCookie(name, value) {
    var domainParts = /([^\.]+\.[^\.]+)$/.exec(document.location.hostname);
    var domain = domainParts[1];
    document.cookie = "" + name + "=" + value + "; "
        + "path=/; "
        + (domain ? ("domain=" + domain + ";") : '');
}

/**
 * Try to read cookie.
 *
 * @param {String} name Cookie name
 * @returns {String|Boolean} Cookie value or boolean false if cookie with
 * specified name does not exist
 */
function mibewReadCookie(name) {
    var cookies = document.cookie.split('; ');
    var nameForSearch = name + '=';
    var value = false;
    for (var i = 0; i < cookies.length; i++) {
        if (cookies[i].indexOf(nameForSearch) != -1) {
            value = cookies[i].substr(nameForSearch.length);
            break;
        }
    }
    return value;
}

/**
 * Update user id. API function
 * @param {Object} response Data object from server
 */
function mibewUpdateUserId(response) {
    mibewCreateCookie(mibewVisitorCookieName, response.user.id);
}
