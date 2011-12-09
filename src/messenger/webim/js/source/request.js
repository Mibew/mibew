var mibewRequestedScripts = new Array();
var mibewHandlers = new Array();
var mibewHandlersDependences = new Array();

function mibewMakeRequest()
{
	mibewDoLoadScript(mibewRequestUrl + '&rnd=' + Math.random(), 'responseScript');
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