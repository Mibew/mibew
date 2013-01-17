/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c,e){b.Views.SendMailControl=b.Views.Control.extend({template:c.templates.send_mail_control,events:e.extend({},b.Views.Control.prototype.events,{click:"sendMail"}),sendMail:function(){var a=this.model.get("link");if(a){var c=this.$el.find(".control-config").eq(0).data("win-params"),d=b.Objects.Models.page.get("style"),a=a.replace(/\&amp\;/g,"&")+(d?"&style="+d:""),a=window.open(a,"ForwardMail",c);null!==a&&(a.focus(),a.opener=window)}}})})(Mibew,Handlebars,_);
