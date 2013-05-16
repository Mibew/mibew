/*
 Copyright 2005-2013 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
var mibewRequestedScripts=[],mibewHandlers=[],mibewHandlersDependences=[];function mibewMakeRequest(){var a=mibewReadCookie(mibewVisitorCookieName);mibewDoLoadScript(mibewRequestUrl+"&rnd="+Math.random()+(!1!==a?"&user_id="+a:""),"responseScript")}
function mibewOnResponse(a){var b=a.load,c=a.handlers,d=a.data;a=a.dependences;for(id in b)b[id]in mibewRequestedScripts||(mibewRequestedScripts[id]=[],mibewRequestedScripts[id].url=b[id],mibewRequestedScripts[id].status="loading",mibewLoadScript(id));for(handler in a)handler in mibewHandlersDependences||(mibewHandlersDependences[handler]=a[handler]);for(b=0;b<c.length;b++){var e=c[b];if(mibewCanRunHandler(c[b]))window[e](d);else c[b]in mibewHandlers||(mibewHandlers[e]=function(){window[e](d)})}mibewCleanUpAfterRequest();
window.setTimeout(mibewMakeRequest,mibewRequestTimeout)}function mibewCleanUpAfterRequest(){document.getElementsByTagName("head")[0].removeChild(document.getElementById("responseScript"))}function mibewDoLoadScript(a,b){var c=document.createElement("script");c.setAttribute("type","text/javascript");c.setAttribute("src",a);c.setAttribute("id",b);document.getElementsByTagName("head")[0].appendChild(c);return c}
function mibewLoadScript(a){var b=mibewDoLoadScript(mibewRequestedScripts[a].url,a);b.onload=function(){mibewScriptReady(a)};b.onreadystatechange=function(){("complete"==this.readyState||"loaded"==this.readyState)&&mibewScriptReady(a)}}function mibewScriptReady(a){mibewRequestedScripts[a].status="ready";for(handlerName in mibewHandlers)mibewCanRunHandler(handlerName)&&(mibewHandlers[handlerName](),delete mibewHandlers[handlerName])}
function mibewCanRunHandler(a){a=mibewHandlersDependences[a];for(var b=0;b<a.length;b++)if("ready"!=mibewRequestedScripts[a[b]].status)return!1;return!0}function mibewCreateCookie(a,b){var c=/([^\.]+\.[^\.]+)$/.exec(document.location.hostname)[1];document.cookie=""+a+"="+b+"; path=/; "+(c?"domain="+c+";":"")}function mibewReadCookie(a){var b=document.cookie.split("; ");a+="=";for(var c=!1,d=0;d<b.length;d++)if(-1!=b[d].indexOf(a)){c=b[d].substr(a.length);break}return c}
function mibewUpdateUserId(a){mibewCreateCookie(mibewVisitorCookieName,a.user.id)};
