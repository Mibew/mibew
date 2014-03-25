/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(e,c){c.registerHelper("apply",function(a,b){var f=a,e=/^[0-9A-z_]+$/;b=b.split(/\s*,\s*/);for(var d in b)if(b.hasOwnProperty(d)&&e.test(b[d])){if("function"!=typeof c.helpers[b[d]])throw Error("Unregistered helper '"+b[d]+"'!");f=c.helpers[b[d]](f).toString()}return new c.SafeString(f)});c.registerHelper("allowTags",function(a){a=a.replace(/&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,"<$1>$2</$1>");a=a.replace(/&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,'<span class="$1">$2</span>');
return new c.SafeString(a)});c.registerHelper("formatTime",function(a){var b=new Date(1E3*a);a=b.getHours().toString();var c=b.getMinutes().toString(),b=b.getSeconds().toString();return(10>a?"0"+a:a)+":"+(10>c?"0"+c:c)+":"+(10>b?"0"+b:b)});c.registerHelper("urlReplace",function(a){return new c.SafeString(a.replace(/((?:https?|ftp):\/\/\S*)/g,'<a href="$1" target="_blank">$1</a>'))});c.registerHelper("nl2br",function(a){return new c.SafeString(a.replace(/\n/g,"<br/>"))});c.registerHelper("l10n",function(a){return e.Localization.get(a)||
""});c.registerHelper("ifEven",function(a,b){return 0===a%2?b.fn(this):b.inverse(this)});c.registerHelper("ifOdd",function(a,b){return 0!==a%2?b.fn(this):b.inverse(this)})})(Mibew,Handlebars);
