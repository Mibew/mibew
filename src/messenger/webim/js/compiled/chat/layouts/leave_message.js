/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Layouts.LeaveMessage=b.Marionette.Layout.extend({template:Handlebars.templates.leave_message_layout,regions:{leaveMessageFormRegion:"#content-wrapper",descriptionRegion:"#description-region"},serializeData:function(){return{page:a.Objects.Models.page.toJSON()}}})})(Mibew,Backbone);
