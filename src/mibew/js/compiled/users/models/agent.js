/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t){e.Models.Agent=e.Models.User.extend({defaults:t.extend({},e.Models.User.prototype.defaults,{id:null,isAgent:!0,away:!1}),away:function(){this.setAvailability(!1)},available:function(){this.setAvailability(!0)},setAvailability:function(t){var a=t?"available":"away",i=this;e.Objects.server.callFunctions([{"function":a,arguments:{agentId:this.id,references:{},"return":{}}}],function(e){0==e.errorCode&&i.set({away:!t})},!0)}})}(Mibew,_);