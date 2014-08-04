/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,s){e.Views.Status=t.Marionette.ItemView.extend({template:s.templates["chat/status/base"],className:"status",modelEvents:{change:"render"},onBeforeRender:function(){this.model.get("visible")?this.$el.show():this.$el.hide()}})}(Mibew,Backbone,Handlebars);