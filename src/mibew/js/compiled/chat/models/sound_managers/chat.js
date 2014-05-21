/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){a.Models.ChatSoundManager=a.Models.BaseSoundManager.extend({defaults:c.extend({},a.Models.BaseSoundManager.prototype.defaults,{skipNextMessageSound:!1}),initialize:function(){var b=a.Objects,c=this;b.Collections.messages.on("multiple:add",this.playNewMessageSound,this);b.Models.messageForm.on("before:post",function(){c.set({skipNextMessageSound:!0})})},playNewMessageSound:function(){if(!this.get("skipNextMessageSound")){var b=a.Objects.Models.page.get("mibewRoot");"undefined"!==typeof b&&
this.play(b+"/sounds/new_message")}this.set({skipNextMessageSound:!1})}})})(Mibew,_);
