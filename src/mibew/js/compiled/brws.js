/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
var myAgent="",myVer=0,myRealAgent="";function detectAgent(){for(var a="opera msie safari firefox netscape mozilla".split(" "),d=navigator.userAgent.toLowerCase(),b=0;b<a.length;b++){var c=a[b];if(-1!=d.indexOf(c)){myAgent=c;if(!window.RegExp)break;null!=RegExp(c+"[ /]?([0-9]+(.[0-9]+)?)").exec(d)&&(myVer=parseFloat(RegExp.$1));break}}myRealAgent=myAgent;"Gecko"==navigator.product&&(myAgent="moz")}detectAgent();function getEl(a){return document.getElementById(a)};
