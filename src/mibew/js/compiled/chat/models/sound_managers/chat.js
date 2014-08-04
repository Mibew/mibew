/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,s){e.Models.ChatSoundManager=e.Models.BaseSoundManager.extend({defaults:s.extend({},e.Models.BaseSoundManager.prototype.defaults,{skipNextMessageSound:!1}),initialize:function(){var s=e.Objects,t=this;s.Collections.messages.on("multiple:add",this.playNewMessageSound,this),s.Models.messageForm.on("before:post",function(){t.set({skipNextMessageSound:!0})})},playNewMessageSound:function(){if(!this.get("skipNextMessageSound")){var s=e.Objects.Models.page.get("mibewRoot");"undefined"!=typeof s&&(s+="/sounds/new_message",this.play(s))}this.set({skipNextMessageSound:!1})}})}(Mibew,_);