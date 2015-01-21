/*
 This file is a part of Mibew Messenger.
 http://mibew.org

 Copyright (c) 2005-2014 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var myAgent="",myVer=0,myRealAgent="";function detectAgent(){for(var a="opera msie safari firefox netscape mozilla".split(" "),b=navigator.userAgent.toLowerCase(),c=0;c<a.length;c++){var d=a[c];if(-1!=b.indexOf(d)){myAgent=d;if(!window.RegExp)break;null!=(new RegExp(d+"[ /]?([0-9]+(.[0-9]+)?)")).exec(b)&&(myVer=parseFloat(RegExp.$1));break}}myRealAgent=myAgent;"Gecko"==navigator.product&&(myAgent="moz")}detectAgent();function getEl(a){return document.getElementById(a)};
