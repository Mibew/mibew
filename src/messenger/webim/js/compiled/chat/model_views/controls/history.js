/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,d,e){b.Views.HistoryControl=b.Views.Control.extend({template:d.templates.chat_controls_history,events:e.extend({},b.Views.Control.prototype.events,{click:"showHistory"}),showHistory:function(){var c=b.Objects.Models.user,a=this.model.get("link");c.get("isAgent")&&a&&(c=this.model.get("windowParams"),a=a.replace("&amp;","&","g"),a=window.open(a,"UserHistory",c),null!==a&&(a.focus(),a.opener=window))}})})(Mibew,Handlebars,_);
