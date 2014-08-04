/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,i){e.Views.Agent=t.Marionette.ItemView.extend({template:i.templates.agent,tagName:"span",className:"agent",modelEvents:{change:"render"},initialize:function(){this.isModelFirst=!1,this.isModelLast=!1},serializeData:function(){var e=this.model.toJSON();return e.isFirst=this.isModelFirst,e.isLast=this.isModelLast,e}})}(Mibew,Backbone,Handlebars);