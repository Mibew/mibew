/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,o){e.Views.SecureModeControl=e.Views.Control.extend({template:t.templates["chat/controls/secure_mode"],events:o.extend({},e.Views.Control.prototype.events,{click:"secure"}),secure:function(){var t=this.model.get("link");if(t){var o=e.Objects.Models.page.get("style");window.location.href=t.replace(/\&amp\;/g,"&")+(o?"&style="+o:"")}}})}(Mibew,Handlebars,_);