/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,a,t){e.Views.Avatar=a.Marionette.ItemView.extend({template:t.templates["chat/avatar"],className:"avatar",modelEvents:{change:"render"}})}(Mibew,Backbone,Handlebars);