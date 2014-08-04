/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,o,t){e.Views.CloseControl=e.Views.Control.extend({template:o.templates["chat/controls/close"],events:t.extend({},e.Views.Control.prototype.events,{click:"closeThread"}),closeThread:function(){var o=e.Localization.get("Are you sure want to leave chat?");(o===!1||confirm(o))&&this.model.closeThread()}})}(Mibew,Handlebars,_);