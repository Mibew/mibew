/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,o){e.Models.SoundControl=e.Models.Control.extend({defaults:o.extend({},e.Models.Control.prototype.defaults,{enabled:!0}),toggle:function(){var o=!this.get("enabled");e.Objects.Models.soundManager.set({enabled:o}),this.set({enabled:o})},getModelType:function(){return"SoundControl"}})}(Mibew,_);