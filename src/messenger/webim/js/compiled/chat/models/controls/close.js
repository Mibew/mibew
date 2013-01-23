/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){a.Models.CloseControl=a.Models.Control.extend({getModelType:function(){return"CloseControl"},closeThread:function(){var b=a.Objects.Models.thread,c=a.Objects.Models.user;a.Objects.server.callFunctions([{"function":"close",arguments:{references:{},"return":{closed:"closed"},threadId:b.get("id"),token:b.get("token"),lastId:b.get("lastId"),user:!c.get("isAgent")}}],function(b){b.closed?window.close():a.Objects.Models.Status.message.setMessage(b.errorMessage||"Cannot close")},!0)}})})(Mibew);
