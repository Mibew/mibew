/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(e,b){b.registerHelper("apply",function(a,c){var f=a,e=/^[0-9A-z_]+$/;c=c.split(/\s*,\s*/);for(var d in c)if(c.hasOwnProperty(d)&&e.test(c[d])){if("function"!=typeof b.helpers[c[d]])throw Error("Unregistered helper '"+c[d]+"'!");f=b.helpers[c[d]](f).toString()}return new b.SafeString(f)});b.registerHelper("allowTags",function(a){a=a.replace(/&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,"<$1>$2</$1>");a=a.replace(/&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,'<span class="$1">$2</span>');
return new b.SafeString(a)});b.registerHelper("formatTime",function(a){var c=new Date(1E3*a);a=c.getHours().toString();var b=c.getMinutes().toString(),c=c.getSeconds().toString();return(10>a?"0"+a:a)+":"+(10>b?"0"+b:b)+":"+(10>c?"0"+c:c)});b.registerHelper("urlReplace",function(a){return new b.SafeString(a.replace(/((?:https?|ftp):\/\/\S*)/g,'<a href="$1" target="_blank">$1</a>'))});b.registerHelper("nl2br",function(a){return new b.SafeString(a.replace(/\n/g,"<br/>"))});b.registerHelper("L10n",function(a){return e.Localization.get(a)||
""})})(Mibew,Handlebars);
