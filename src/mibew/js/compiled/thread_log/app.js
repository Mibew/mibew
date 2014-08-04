/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,s){var i=new s.Marionette.Application;i.addRegions({messagesRegion:"#messages-region"}),i.addInitializer(function(s){var n=new e.Collections.Messages;e.Objects.Collections.messages=n,n.updateMessages(s.messages),i.messagesRegion.show(new e.Views.MessagesCollection({collection:n}))}),e.Application=i}(Mibew,Backbone);