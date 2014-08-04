/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,s){e.Collections.Messages=s.Collection.extend({model:e.Models.Message,updateMessages:function(e){for(var s=[],i=0;i<e.length;i++)e[i].message&&s.push(e[i]);s.length>0&&this.add(s)}})}(Mibew,Backbone,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Views.MessagesCollection=e.Views.CollectionBase.extend({itemView:e.Views.Message,className:"messages-collection"})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,s){var i=new s.Marionette.Application;i.addRegions({messagesRegion:"#messages-region"}),i.addInitializer(function(s){var n=new e.Collections.Messages;e.Objects.Collections.messages=n,n.updateMessages(s.messages),i.messagesRegion.show(new e.Views.MessagesCollection({collection:n}))}),e.Application=i}(Mibew,Backbone);