/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,o){e.Views.SoundControl=e.Views.Control.extend({template:t.templates["chat/controls/sound"],events:o.extend({},e.Views.Control.prototype.events,{click:"toggle"}),toggle:function(){this.model.toggle()}})}(Mibew,Handlebars,_);