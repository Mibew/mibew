/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
Handlebars.registerHelper("apply",function(b,a){var c=b,e=/^[0-9A-z_]+$/,a=a.split(/\s*,\s*/),d;for(d in a)if(a.hasOwnProperty(d)&&e.test(a[d])){if("function"!=typeof Handlebars.helpers[a[d]])throw Error("Unregistered helper '"+a[d]+"'!");c=Handlebars.helpers[a[d]](c).toString()}return new Handlebars.SafeString(c)});
Handlebars.registerHelper("formatTime",function(b){var a=new Date(1E3*b),b=a.getHours().toString(),c=a.getMinutes().toString(),a=a.getSeconds().toString();return(10<b?b:"0"+b)+":"+(10<c?c:"0"+c)+":"+(10<a?a:"0"+a)});Handlebars.registerHelper("urlReplace",function(b){return new Handlebars.SafeString(b.replace(/((?:https?|ftp):\/\/\S*)/g,'<a href="$1" target="_blank">$1</a>'))});Handlebars.registerHelper("nl2br",function(b){return new Handlebars.SafeString(b.replace(/\n/g,"<br/>"))});