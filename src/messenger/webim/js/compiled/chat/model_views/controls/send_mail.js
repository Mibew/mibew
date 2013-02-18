/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,d,e){b.Views.SendMailControl=b.Views.Control.extend({template:d.templates.send_mail_control,events:e.extend({},b.Views.Control.prototype.events,{click:"sendMail"}),sendMail:function(){var a=this.model.get("link"),c=b.Objects.Models.page;if(a){var d=this.model.get("windowParams"),c=c.get("style"),a=a.replace(/\&amp\;/g,"&")+(c?"&style="+c:""),a=window.open(a,"ForwardMail",d);null!==a&&(a.focus(),a.opener=window)}}})})(Mibew,Handlebars,_);
