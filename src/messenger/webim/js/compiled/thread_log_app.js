;(function(a,b){a.Collections.Messages=b.Collection.extend({model:a.Models.Message})})(Mibew,Backbone,_);
(function(a){a.Views.MessagesCollection=a.Views.CollectionBase.extend({itemView:a.Views.Message,className:"messages-collection"})})(Mibew);
(function(b,c){var a=new c.Marionette.Application;a.addRegions({messagesRegion:"#messages-region"});a.addInitializer(function(c){a.messagesRegion.show(new b.Views.MessagesCollection({collection:new b.Collections.Messages(c.messages)}))});b.Application=a})(Mibew,Backbone);
