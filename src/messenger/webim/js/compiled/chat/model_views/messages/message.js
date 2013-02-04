/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c,d,e){var f={"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},g=/[&<>'"`]/g;c.Views.Message=d.Marionette.ItemView.extend({template:e.templates.message,className:"message",modelEvents:{change:"render"},serializeData:function(){var a=this.model.toJSON(),b=this.model.get("kind");a.allowFormatting=b!=this.model.KIND_USER&&b!=this.model.KIND_AGENT;a.kindName=this.kindToString(b);a.message=this.escapeString(a.message);return a},kindToString:function(a){return a==this.model.KIND_USER?
"user":a==this.model.KIND_AGENT?"agent":a==this.model.KIND_FOR_AGENT?"hidden":a==this.model.KIND_INFO?"inf":a==this.model.KIND_CONN?"conn":a==this.model.KIND_EVENTS?"event":""},escapeString:function(a){return a.replace(g,function(a){return f[a]||"&amp;"})}})})(Mibew,Backbone,Handlebars);
