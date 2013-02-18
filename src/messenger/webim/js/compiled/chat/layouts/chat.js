/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Layouts.Chat=b.Marionette.Layout.extend({template:Handlebars.templates.chat_layout,regions:{controlsRegion:"#controls-region",avatarRegion:"#avatar-region",messagesRegion:{selector:"#messages-region",regionType:a.Regions.Messages},statusRegion:"#status-region",messageFormRegion:"#message-form-region",soundRegion:"#sound-region"},serializeData:function(){return{user:a.Objects.Models.user.toJSON()}}})})(Mibew,Backbone);
