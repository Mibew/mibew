/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b,c){a.Views.SoundControl=a.Views.Control.extend({template:b.templates.chat_controls_sound,events:c.extend({},a.Views.Control.prototype.events,{click:"toggle"}),toggle:function(){this.model.toggle()}})})(Mibew,Handlebars,_);
