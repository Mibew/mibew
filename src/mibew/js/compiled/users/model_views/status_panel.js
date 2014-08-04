/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,n){e.Views.StatusPanel=t.Marionette.ItemView.extend({template:n.templates.status_panel,modelEvents:{change:"render"},ui:{changeStatus:"#change-status"},events:{"click #change-status":"changeAgentStatus"},initialize:function(){e.Objects.Models.agent.on("change",this.render,this)},changeAgentStatus:function(){this.model.changeAgentStatus()},serializeData:function(){var t=this.model.toJSON();return t.agent=e.Objects.Models.agent.toJSON(),t}})}(Mibew,Backbone,Handlebars);