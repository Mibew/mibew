/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b,c){a.Views.LeaveMessageDescription=b.Marionette.ItemView.extend({template:c.templates.leave_message_description,serializeData:function(){return{page:a.Objects.Models.page.toJSON()}}})})(Mibew,Backbone,Handlebars);
