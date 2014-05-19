/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(k,b){var e=!1,f,h=function(){if(0!=b("#messages-region").size()){var a=b("#messages-region"),c=b("#avatar-region");if(!1===e){var d=0;b("body > *").each(function(){var a=b(this),c=a.offset(),a=a.height();d<c.top+a&&(d=c.top+a)});e=d-a.height()}var g=b(window).height()-e;g<parseInt(a.css("minHeight"))||(a.height(g),0<c.size()&&c.height(a.innerHeight()))}},l=function(){f&&clearTimeout(f);f=setTimeout(h,0)};k.Application.Chat.addInitializer(function(){var a=b("img").size(),c=0,d=function(){c++;
a==c&&(h(),b("#messages-region").scrollTop(b("#messages-region").prop("scrollHeight")),b(window).resize(l))};b("img").each(function(){var a=b(this);0<a.height()?d():a.load(d)})})})(Mibew,jQuery);
