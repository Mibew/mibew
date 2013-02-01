/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c,d){a.Views.VisitorsCollection=b.Marionette.CompositeView.extend({template:c.templates.visitors_collection,itemView:a.Views.Visitor,itemViewContainer:"#visitors-container",emptyView:a.Views.NoVisitors,className:"visitors-collection",collectionEvents:{sort:"renderCollection"},itemViewOptions:function(b){return{tagName:a.Objects.Models.page.get("visitorTag"),collection:b.get("controls")}},initialize:function(){window.setInterval(d.bind(this.renderCollection,this),2E3);this.on("itemview:before:render",
this.updateStyles,this)}})})(Mibew,Backbone,Handlebars,_);
