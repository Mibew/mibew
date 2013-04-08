/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.VisitorsCollection=a.Views.CompositeBase.extend({template:b.templates.visitors_collection,itemView:a.Views.Visitor,itemViewContainer:"#visitors-container",emptyView:a.Views.NoVisitors,className:"visitors-collection",collectionEvents:{sort:"renderCollection"},itemViewOptions:function(b){return{tagName:a.Objects.Models.page.get("visitorTag"),collection:b.get("controls")}},initialize:function(){window.setInterval(c.bind(this.updateTimers,this),2E3);this.on("render",this.updateTimers,
this)},updateTimers:function(){a.Utils.updateTimers(this.$el,".timesince")}})})(Mibew,Handlebars,_);
