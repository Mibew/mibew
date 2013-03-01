/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.Status=b.Marionette.ItemView.extend({template:c.templates.chat_status_base,className:"status",modelEvents:{change:"render"},onBeforeRender:function(){this.model.get("visible")?this.$el.show():this.$el.hide()}})})(Mibew,Backbone,Handlebars);
