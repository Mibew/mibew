/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.LeaveMessageDescription=b.Marionette.ItemView.extend({template:c.templates.leave_message_description,serializeData:function(){return{page:a.Objects.Models.page.toJSON()}}})})(Mibew,Backbone,Handlebars);
