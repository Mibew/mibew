/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(i,e){var t,n=!1,o=function(){if(0!=e("#messages-region").size()){var i=e("#messages-region"),t=e("#avatar-region");if(n===!1){var o=0;e("body > *").each(function(){var i=e(this),t=i.offset(),n=i.height();o<t.top+n&&(o=t.top+n)}),n=o-i.height()}var s=e(window).height()-n;s<parseInt(i.css("minHeight"))||(i.height(s),t.size()>0&&t.height(i.innerHeight()))}},s=function(){t&&clearTimeout(t),t=setTimeout(o,0)};i.Application.Chat.addInitializer(function(){var i=e("img").size(),t=0,n=function(){t++,i==t&&(o(),e("#messages-region").scrollTop(e("#messages-region").prop("scrollHeight")),e(window).resize(s))};e("img").each(function(){var i=e(this);i.height()>0?n():i.load(n)})})}(Mibew,jQuery);