/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(e,a){a.registerHelper("apply",function(c,b){var f=c,e=/^[0-9A-z_]+$/;b=b.split(/\s*,\s*/);for(var d in b)if(b.hasOwnProperty(d)&&e.test(b[d])){if("function"!=typeof a.helpers[b[d]])throw Error("Unregistered helper '"+b[d]+"'!");f=a.helpers[b[d]](f).toString()}return new a.SafeString(f)});a.registerHelper("formatTime",function(c){var b=new Date(1E3*c);c=b.getHours().toString();var a=b.getMinutes().toString(),b=b.getSeconds().toString();return(10<c?c:"0"+c)+":"+(10<a?a:"0"+a)+":"+(10<b?b:
"0"+b)});a.registerHelper("urlReplace",function(c){return new a.SafeString(c.replace(/((?:https?|ftp):\/\/\S*)/g,'<a href="$1" target="_blank">$1</a>'))});a.registerHelper("nl2br",function(c){return new a.SafeString(c.replace(/\n/g,"<br/>"))});a.registerHelper("L10n",function(a){return e.Localization.get(a)||""})})(Mibew,Handlebars);
