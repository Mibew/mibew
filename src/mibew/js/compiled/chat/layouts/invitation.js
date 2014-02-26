/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b){a.Layouts.Invitation=b.Marionette.Layout.extend({template:Handlebars.templates.invitation_layout,regions:{messagesRegion:{selector:"#invitation-messages-region",regionType:a.Regions.Messages}}})})(Mibew,Backbone);
