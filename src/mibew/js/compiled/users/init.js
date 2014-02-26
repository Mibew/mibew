/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,e){b.Regions={};b.Popup={};b.Popup.open=function(a,c,d){c=c.replace(/[^A-z0-9_]+/g,"");a=window.open(a,c,d);a.focus();a.opener=window};b.Utils.updateTimers=function(a,c){a.find(c).each(function(){var d=e(this).data("timestamp");if(d){var a=Math.round((new Date).getTime()/1E3)-d,d=a%60,c=Math.floor(a/60)%60,a=Math.floor(a/3600),b=[];0<a&&b.push(a);b.push(10>c?"0"+c:c);b.push(10>d?"0"+d:d);e(this).html(b.join(":"))}})}})(Mibew,jQuery);
