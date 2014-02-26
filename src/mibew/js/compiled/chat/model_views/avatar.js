/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b,c){a.Views.Avatar=b.Marionette.ItemView.extend({template:c.templates.chat_avatar,className:"avatar",modelEvents:{change:"render"}})})(Mibew,Backbone,Handlebars);
