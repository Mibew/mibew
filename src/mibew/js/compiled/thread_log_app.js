/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(d,e){d.Collections.Messages=e.Collection.extend({model:d.Models.Message,updateMessages:function(b){for(var c=[],a=0;a<b.length;a++)b[a].message&&c.push(b[a]);0<c.length&&this.add(c)}})})(Mibew,Backbone,_);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a){a.Views.MessagesCollection=a.Views.CollectionBase.extend({itemView:a.Views.Message,className:"messages-collection"})})(Mibew);
/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){var b=new c.Marionette.Application;b.addRegions({messagesRegion:"#messages-region"});b.addInitializer(function(c){var d=new a.Collections.Messages;a.Objects.Collections.messages=d;d.updateMessages(c.messages);b.messagesRegion.show(new a.Views.MessagesCollection({collection:d}))});a.Application=b})(Mibew,Backbone);
