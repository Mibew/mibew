/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){a.Models.SoundControl=a.Models.Control.extend({defaults:c.extend({},a.Models.Control.prototype.defaults,{enabled:!0}),toggle:function(){var b=!this.get("enabled");a.Objects.Models.soundManager.set({enabled:b});this.set({enabled:b})},getModelType:function(){return"SoundControl"}})})(Mibew,_);
