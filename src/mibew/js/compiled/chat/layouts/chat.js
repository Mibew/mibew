/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,a){e.Layouts.Chat=a.Marionette.Layout.extend({template:Handlebars.templates["chat/layout"],regions:{controlsRegion:"#controls-region",avatarRegion:"#avatar-region",messagesRegion:{selector:"#messages-region",regionType:e.Regions.Messages},statusRegion:"#status-region",messageFormRegion:"#message-form-region"},serializeData:function(){var a=e.Objects.Models;return{page:a.page.toJSON(),user:a.user.toJSON()}}})}(Mibew,Backbone);