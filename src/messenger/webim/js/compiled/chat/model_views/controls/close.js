/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,c,d){a.Views.CloseControl=a.Views.Control.extend({template:c.templates.close_control,events:d.extend({},a.Views.Control.prototype.events,{click:"closeThread"}),closeThread:function(){var b=a.Localization.get("chat.close.confirmation");(!1===b||confirm(b))&&this.model.closeThread()}})})(Mibew,Handlebars,_);
