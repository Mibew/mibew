/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function detectAgent(){for(var e=["opera","msie","safari","firefox","netscape","mozilla"],t=navigator.userAgent.toLowerCase(),n=0;n<e.length;n++){var a=e[n];if(-1!=t.indexOf(a)){if(myAgent=a,!window.RegExp)break;var r=new RegExp(a+"[ /]?([0-9]+(.[0-9]+)?)");null!=r.exec(t)&&(myVer=parseFloat(RegExp.$1));break}}myRealAgent=myAgent,"Gecko"==navigator.product&&(myAgent="moz")}function getEl(e){return document.getElementById(e)}var myAgent="",myVer=0,myRealAgent="";detectAgent();