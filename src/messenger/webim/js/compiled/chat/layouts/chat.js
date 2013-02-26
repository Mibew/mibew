/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,c){a.Layouts.Chat=c.Marionette.Layout.extend({template:Handlebars.templates.chat_layout,regions:{controlsRegion:"#controls-region",avatarRegion:"#avatar-region",messagesRegion:{selector:"#messages-region",regionType:a.Regions.Messages},statusRegion:"#status-region",messageFormRegion:"#message-form-region"},serializeData:function(){var b=a.Objects.Models;return{page:b.page.toJSON(),user:b.user.toJSON()}}})})(Mibew,Backbone);
