/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){e.Models.MessageForm=e.Models.Base.extend({defaults:{predefinedAnswers:[],ignoreCtrl:!1},postMessage:function(t){var s=e.Objects.Models.thread,r=e.Objects.Models.user;if(r.get("canPost")){this.trigger("before:post",this);var n=this;e.Objects.server.callFunctions([{"function":"post",arguments:{references:{},"return":{},message:t,threadId:s.get("id"),token:s.get("token"),user:!r.get("isAgent")}}],function(){n.trigger("after:post",n)},!0)}}})}(Mibew);