/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c,e){b.Views.HistoryControl=b.Views.Control.extend({template:c.templates.history_control,events:e.extend({},b.Views.Control.prototype.events,{click:"showHistory"}),showHistory:function(){var d=b.Objects.Models.user,c=b.Objects.Models.page,a=this.model.get("link");d.get("isAgent")&&a&&(d=c.get("historyWindowParams"),a=a.replace("&amp;","&","g"),a=window.open(a,"UserHistory",d),null!==a&&(a.focus(),a.opener=window))}})})(Mibew,Handlebars,_);
