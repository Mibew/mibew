/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.Avatar=b.Marionette.ItemView.extend({template:c.templates.chat_avatar,className:"avatar",modelEvents:{change:"render"}})})(Mibew,Backbone,Handlebars);
