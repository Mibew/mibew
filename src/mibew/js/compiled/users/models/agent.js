/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,b){a.Models.Agent=a.Models.User.extend({defaults:b.extend({},a.Models.User.prototype.defaults,{id:null,isAgent:!0,away:!1}),away:function(){this.setAvailability(!1)},available:function(){this.setAvailability(!0)},setAvailability:function(c){var b=this;a.Objects.server.callFunctions([{"function":c?"available":"away",arguments:{agentId:this.id,references:{},"return":{}}}],function(a){0==a.errorCode&&b.set({away:!c})},!0)}})})(Mibew,_);
