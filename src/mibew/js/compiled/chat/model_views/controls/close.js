/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c,d){a.Views.CloseControl=a.Views.Control.extend({template:c.templates.chat_controls_close,events:d.extend({},a.Views.Control.prototype.events,{click:"closeThread"}),closeThread:function(){var b=a.Localization.get("chat.close.confirmation");(!1===b||confirm(b))&&this.model.closeThread()}})})(Mibew,Handlebars,_);
