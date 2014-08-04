/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){e.Models.CloseControl=e.Models.Control.extend({getModelType:function(){return"CloseControl"},closeThread:function(){var s=e.Objects.Models.thread,t=e.Objects.Models.user;e.Objects.server.callFunctions([{"function":"close",arguments:{references:{},"return":{closed:"closed"},threadId:s.get("id"),token:s.get("token"),lastId:s.get("lastId"),user:!t.get("isAgent")}}],function(s){s.closed?window.close():e.Objects.Models.Status.message.setMessage(s.errorMessage||"Cannot close")},!0)}})}(Mibew);