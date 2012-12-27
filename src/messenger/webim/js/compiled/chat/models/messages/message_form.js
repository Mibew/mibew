/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){a.Models.MessageForm=a.Models.Base.extend({defaults:{predefinedAnswers:[],ignoreCtrl:!1},postMessage:function(e){var b=a.Objects.thread,c=a.Objects.Models.user;if(c.get("canPost")){this.trigger("before:post",this);var d=this;a.Objects.server.callFunctions([{"function":"post",arguments:{references:{},"return":{},message:e,threadId:b.threadId,token:b.token,user:!c.get("isAgent")}}],function(){d.trigger("after:post",d)},!0)}}})})(Mibew);
