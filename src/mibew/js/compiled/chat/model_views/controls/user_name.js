/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,n,t){e.Views.UserNameControl=e.Views.Control.extend({template:n.templates["chat/controls/user_name"],events:t.extend({},e.Views.Control.prototype.events,{"click .user-name-control-set":"changeName","click .user-name-control-change":"showNameInput","keydown #user-name-control-input":"inputKeyDown"}),ui:{nameInput:"#user-name-control-input"},initialize:function(){e.Objects.Models.user.on("change:name",this.hideNameInput,this),this.nameInput=e.Objects.Models.user.get("defaultName")},serializeData:function(){var n=this.model.toJSON();return n.user=e.Objects.Models.user.toJSON(),n.nameInput=this.nameInput,n},inputKeyDown:function(e){var n=e.which;(13==n||10==n)&&this.changeName()},hideNameInput:function(){this.nameInput=!1,this.render()},showNameInput:function(){this.nameInput=!0,this.render()},changeName:function(){var e=this.ui.nameInput.val();this.model.changeName(e)}})}(Mibew,Handlebars,_);