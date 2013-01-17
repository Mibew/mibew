/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,d,e){a.Views.SecureModeControl=a.Views.Control.extend({template:d.templates.secure_mode_control,events:e.extend({},a.Views.Control.prototype.events,{click:"secure"}),secure:function(){var b=this.model.get("link");if(b){var c=a.Objects.Models.page.get("style");window.location.href=b.replace(/\&amp\;/g,"&")+(c?"&style="+c:"")}}})})(Mibew,Handlebars,_);
