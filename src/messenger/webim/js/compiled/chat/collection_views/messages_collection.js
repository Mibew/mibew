/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){a.Views.MessagesCollection=a.Views.CollectionBase.extend({itemView:a.Views.Message,className:"messages-collection",initialize:function(){this.collection.on("multiple:add",this.messagesAdded,this);a.Objects.Models.messageForm.on("before:post",this.messagePost,this)},skipNextSound:!0,messagePost:function(){this.skipNextSound=!0},messagesAdded:function(){if(!this.skipNextSound&&a.Objects.Models.Controls.sound.get("enabled")){var b=a.Objects.Models.page.get("webimRoot");b&&a.Utils.playSound(b+
"/sounds/new_message.wav")}this.skipNextSound=!1}})})(Mibew);
