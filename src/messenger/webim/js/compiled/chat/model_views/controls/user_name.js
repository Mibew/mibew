/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c,d){b.Views.UserNameControl=b.Views.Control.extend({template:c.templates.chat_controls_user_name,events:d.extend({},b.Views.Control.prototype.events,{"click .user-name-control-set":"changeName","click .user-name-control-change":"showNameInput","keydown #user-name-control-input":"inputKeyDown"}),ui:{nameInput:"#user-name-control-input"},initialize:function(){b.Objects.Models.user.on("change:name",this.hideNameInput,this);this.nameInput=b.Objects.Models.user.get("defaultName")},serializeData:function(){var a=
this.model.toJSON();a.user=b.Objects.Models.user.toJSON();a.nameInput=this.nameInput;return a},inputKeyDown:function(a){a=a.which;(13==a||10==a)&&this.changeName()},hideNameInput:function(){this.nameInput=!1;this.render()},showNameInput:function(){this.nameInput=!0;this.render()},changeName:function(){var a=this.ui.nameInput.val();this.model.changeName(a)}})})(Mibew,Handlebars,_);
